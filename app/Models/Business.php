<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/17/2015
 * Time: 11:12 AM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model{

    protected $table = 'business';
    protected $primaryKey = 'business_id';
    public $timestamps = false;

    public static function getBusinessIdByTerminalId($terminal_id)
    {
        return Business::getBusinessIdByServiceId(Terminal::serviceId($terminal_id));
    }

    public static function getBusinessIdByServiceId($service_id)
    {
        return Branch::businessId(Service::branchId($service_id));
    }

    public static function searchSuggest($keyword){
        return Business::where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('local_address', 'LIKE', '%' . $keyword . '%')
            ->select(array('name', 'local_address'))
            ->get()
            ->toArray();
    }

    public static function getBusinessByNameCountryIndustryTimeopen($name, $country, $industry, $time_open = null, $timezone = null)
    {
        //parse the time string
        if ($time_open) {
            $time_open_arr = Helper::parseTime($time_open);
        }

        //check for missing idustry values
        if ($industry == 'Industry') {
            $industry = '';
        }

        //check if timezone is numeric
        if(is_numeric($timezone)){
            $timezones = Helper::timezoneOffsetToNameArray($timezone);
        }else{
            $timezones = [$timezone];
        }

        //ARA this makes editing queries easier
        //query for business name
        $query = Business::where('name', 'LIKE', '%' . $name . '%');

        //query for country/location
        if($country){
            $query->where('latitude', '<=', $country['ne_lat'])
                ->where('latitude', '>=', $country['sw_lat'])
                ->where('longitude', '<=', $country['ne_lng'])
                ->where('longitude', '>=', $country['sw_lng']);
        }

        //query for industry
        if($industry != ''){
            $query->where('industry', 'LIKE', '%' . $industry . '%');
        }

        //query timezone if name is not given
        if($name == ''){
            $query->whereIn('timezone', $timezones);
        }

        //query for time open
        if($time_open){
            if($time_open_arr['hour'] < 12){
                $query->where('open_ampm', '=', $time_open_arr['ampm'])
                    ->where('open_hour', '!=', '12')
                    ->where('open_hour', '>=', $time_open_arr['hour'])
                    ->where('open_minute', '>=', $time_open_arr['min']);
            }elseif($time_open_arr['hour'] == 12){
                $query->where('open_ampm', '=', $time_open_arr['ampm'])
                    ->where('open_hour', '<=', '12');
            }
        }

        return $query->get();
    }

    public static function searchBusiness($get){
        $values = [
            'keyword'   => isset($get['keyword']) ? $get['keyword'] : '',
            'industry'  => isset($get['industry']) ? $get['industry'] : '',
            'country'   => isset($get['country']) && $get['country'] != '' ? $get['country'] : 'Philippines',
            'time_open' => isset($get['time_open']) && $get['time_open'] != '' ? $get['time_open'] : null,
            'timezone'  => isset($get['user_timezone']) && $get['timezone'] != '' ? $get['user_timezone'] : 'Asia/Manila',
            'limit'     => isset($get['limit']) && $get['limit'] != '' ? (int) $get['limit'] : 8,
            'offset'    => isset($get['offset']) && $get['offset'] != '' ? (int) $get['offset'] : 0,
        ];

        $values = json_decode(json_encode($values), FALSE);
        if($values->country != ''){
            $geolocation = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$values->country));
            $values->location = array(
                'ne_lat' => $geolocation->results[0]->geometry->bounds->northeast->lat,
                'ne_lng' => $geolocation->results[0]->geometry->bounds->northeast->lng,
                'sw_lat' => $geolocation->results[0]->geometry->bounds->southwest->lat,
                'sw_lng' => $geolocation->results[0]->geometry->bounds->southwest->lng,
            );
        }else{
            $values->location = [];
        }
        $res = Business::getBusinessByNameCountryIndustryTimeopen($values->keyword, $values->location, $values->industry, $values->time_open, $values->timezone);

        $arr = array();
        foreach ($res as $count => $data) {
            $first_service = Service::getFirstServiceOfBusiness($data->business_id);
            $all_numbers = Queue::allNumbers($first_service->service_id);
            $time_open = $data->open_hour . ':' . Helper::doubleZero($data->open_minute) . ' ' . strtoupper($data->open_ampm);
            $time_close = $data->close_hour . ':' . Helper::doubleZero($data->close_minute) . ' ' . strtoupper($data->close_ampm);
            $arr[] = array(
                'business_id' => $data->business_id,
                'business_name' => $data->name,
                'local_address' => $data->local_address,
                'time_open' => Helper::changeBusinessTimeTimezone($time_open, $data->timezone, $values->timezone),
                'time_close' => Helper::changeBusinessTimeTimezone($time_close, $data->timezone, $values->timezone),
                'waiting_time' => Analytics::getWaitingTimeString($data->business_id),

                //ARA more info for business cards
                'last_number_called' => count($all_numbers->called_numbers) > 0 ? $all_numbers->called_numbers[0]['priority_number'] : 'none', //ok
                'next_available_number' => $all_numbers->next_number, //ok
                'last_active' => Analytics::getLastActive($data->business_id),
                'card_bool' => count($all_numbers->called_numbers) > 0 || count($all_numbers->uncalled_numbers) + count($all_numbers->timebound_numbers) > 0,
            );
        }

        return array_slice($arr, $values->offset, $values->limit);
    }

}