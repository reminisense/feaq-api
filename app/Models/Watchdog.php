<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/24/2015
 * Time: 2:22 PM
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchdog extends Model
{
    protected $table = 'watchdog';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    public static function createRecord($val = array())
    {
        return Watchdog::insertGetId($val);
    }

    public static function getUserRecords($user_id = null)
    {
        $user_data = [];
        if ($user_id) {
            $results = Watchdog::where('user_id', '=', $user_id)->get();
        } else {
            $results = Watchdog::all();
        }

        foreach ($results as $index => $data) {
            $user_data[$index] = unserialize($data->value);
            $user_data[$index]['user_id'] = $data->user_id;
            $user_data[$index]['action_type'] = $data->action_type;
        }
        return $user_data;
    }


    public static function queryUserInfo($keyword, $user_id = null)
    {
        $user_records = Watchdog::getUserRecords($user_id);
        $user_queues = Analytics::getUserQueues($user_id);

        $user_data = array_merge($user_records, $user_queues);
        $values = [];
        foreach ($user_data as $data) {
            if ($keyword === 'geolocation' && isset($data['latitude'])) {
                $values[] = ($data['latitude'] . ', ' . $data['longitude']);
            } else if ($keyword === 'broadcast' && isset($data['business_id'])) {
                $business = isset($data->business_id) ? Business::getBusinessByBusinessId($data->business_id) : null;
                $values[] = is_null($business) ? 'Deleted Businesses' : $business->name;
            } else if (isset($data[$keyword])) {
                $values[] = $data[$keyword];
            }
        }
        $numbers = array_count_values($values);
        return $numbers;
    }
}