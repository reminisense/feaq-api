<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 11:22 AM
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QueueSettings extends Model
{
    protected $table = 'queue_settings';
    protected $primaryKey = 'queue_setting_id';
    public $timestamps = false;
    public static function numberStart($service_id, $date = null){
        return QueueSettings::queueSetting('number_start', 1, $service_id, $date);
    }
    public static function numberLimit($service_id, $date = null){
        $business_id = Business::getBusinessIdByServiceId($service_id);
        return Business::find($business_id)->queue_limit;
    }
    public static function terminalSpecificIssue($service_id, $date = null){
        return QueueSettings::queueSetting('terminal_specific_issue', 0, $service_id, $date);
    }
    /**
     * @param $field = field name in db
     * @param $default = default value in case null or no row found
     * @param $service_id
     * @param null $date
     * @return mixed
     */
    public static function queueSetting($field, $default, $service_id, $date = null){
        $date = $date == null ? time() : $date;
        $queue_setting = QueueSettings::getServiceQueueSettings($service_id, $date);
        return isset($queue_setting->$field) && $queue_setting->$field ? $queue_setting->$field : $default;
    }
    public static function getServiceQueueSettings($service_id, $date = null){
        $date = $date == null ? time() : $date;
        $queue_setting = QueueSettings::where('service_id', '=', $service_id)
            ->where('date', '<=', $date)
            ->orderBy('queue_setting_id', 'desc')
            ->orderBy('date', 'asc')
            ->first();
        return $queue_setting;
    }
}