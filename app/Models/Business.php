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
        if ($time_open) {
            $time_open_arr = Helper::parseTime($time_open);
        } else {
            $time_open_arr['hour'] = '';
            $time_open_arr['min'] = '';
            $time_open_arr['ampm'] = '';
        }

        if ($industry == 'Industry') {
            $industry = '';
        }

        if(is_numeric($timezone)){
            $timezones = Helper::timezoneOffsetToNameArray($timezone);
        }else{
            $timezones = [$timezone];
        }

        //ARA this makes editing queries easier
        $query = Business::where('name', 'LIKE', '%' . $name . '%')
            ->where('latitude', '<=', $country['ne_lat'])
            ->where('latitude', '>=', $country['sw_lat'])
            ->where('longitude', '<=', $country['ne_lng'])
            ->where('longitude', '>=', $country['sw_lng'])
            ->where('industry', 'LIKE', '%' . $industry . '%');

        if($name == ''){
            $query->whereIn('timezone', $timezones);
        }

        if ($time_open_arr['ampm'] == 'PM' && $time_open_arr['min'] == '00') {
            $query->where('open_ampm', '=', 'PM')
                ->where('open_hour', '>=', $time_open_arr['hour']);
        } elseif ($time_open_arr['ampm'] == 'PM' && $time_open_arr['min'] == '30') {
            $query->where('open_ampm', '=', 'PM')
                ->whereRaw('open_hour > ? OR (open_hour = ? AND open_minute = ?)',
                    array($time_open_arr['hour'], $time_open_arr['hour'], '30'));
        } elseif ($time_open_arr['ampm'] == 'AM' && $time_open_arr['min'] == '00') {
            $query->whereRaw('(open_hour >= ? AND open_ampm = ?) OR (open_hour < ? AND open_ampm = ?)',
                array($time_open_arr['hour'], 'AM', $time_open_arr['hour'], 'PM'));
        } elseif ($time_open_arr['ampm'] == 'AM' && $time_open_arr['min'] == '30') {
            $query->whereRaw('(open_hour > ? AND open_ampm = ?) OR (open_hour < ? AND open_ampm = ?) OR (open_hour = ? AND open_minute = ? AND open_ampm = ?)',
                array($time_open_arr['hour'], 'AM', $time_open_arr['hour'], 'PM', $time_open_arr['hour'], '30', 'AM'));
        }
        return $query->get();
    }

    public static function searchBusiness($get){
        $values = [
            'keyword'   => isset($_GET['name']) ? $_GET['name'] : '',
            'industry'  => isset($_GET['industry']) ? $_GET['industry'] : '',
            'country'   => isset($_GET['country']) && $_GET['country'] != '' ? $_GET['country'] : 'Philippines',
            'time_open' => isset($_GET['time_open']) && $_GET['time_open'] != '' ? $_GET['time_open'] : null,
            'timezone'  => isset($_GET['user_timezone']) && $_GET['timezone'] != '' ? $_GET['user_timezone'] : 'Asia/Manila',
            'limit'     => isset($_GET['limit']) && $_GET['limit'] != '' ? (int) $_GET['limit'] : 8,
            'offset'    => isset($_GET['offset']) && $_GET['offset'] != '' ? (int) $_GET['offset'] : 0,
        ];

        $values = json_decode(json_encode($values), FALSE);
        $geolocation = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$values->country));
        $values->location = array(
            'ne_lat' => $geolocation->results[0]->geometry->bounds->northeast->lat,
            'ne_lng' => $geolocation->results[0]->geometry->bounds->northeast->lng,
            'sw_lat' => $geolocation->results[0]->geometry->bounds->southwest->lat,
            'sw_lng' => $geolocation->results[0]->geometry->bounds->southwest->lng,
        );
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