<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 11:01 AM
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Analytics extends Model{
    protected $table = 'queue_analytics';
    public $timestamps = false;
    /**
     * requires an array of arrays
     * ex. 'field' => array('conditional_operator', 'value')
     * @param $conditions
     * @return mixed
     */
    public static function getQueueAnalyticsRows($conditions){
        return Helper::getMultipleQueries('queue_analytics', $conditions);
    }
    public static function getBusinessRemainingCount($business_id){
        $uncalled_numbers = 0;
        $branches = Branch::getBranchesByBusinessId($business_id);
        foreach($branches as $branch){
            $uncalled_numbers = Analytics::getBranchRemainingCount($branch->branch_id);
        }
        return $uncalled_numbers;
    }
    public static function getBranchRemainingCount($branch_id){
        $uncalled_numbers = 0;
        $services = Service::getServicesByBranchId($branch_id);
        foreach($services as $service){
            $uncalled_numbers += Analytics::getServiceRemainingCount($service->service_id);
        }
        return $uncalled_numbers;
    }
    public static function getServiceRemainingCount($service_id){
        $all_numbers = Queue::allNumbers($service_id);
        return isset($all_numbers->uncalled_numbers) ? count($all_numbers->uncalled_numbers) : 0;
    }
    public static function getAverageTimeFromActionByBusinessId($action1, $action2, $business_id, $startdate, $enddate){
        return Helper::millisecondsToHMSFormat(Analytics::getAverageTimeValueFromActionByBusinessId($action1, $action2, $business_id, $startdate, $enddate));
    }
    public static function getAverageTimeCalledByBusinessId($business_id, $format = 'string', $startdate, $enddate){
        if($format === 'string'){
            return Analytics::getAverageTimeFromActionByBusinessId(0, 1, $business_id, $startdate, $enddate);
        }else{
            return Analytics::getAverageTimeValueFromActionByBusinessId(0, 1, $business_id, $startdate, $enddate);
        }
    }
    public static function getAverageTimeValueFromActionByBusinessId($action1, $action2, $business_id, $startdate, $enddate){
        $action1_numbers = Analytics::getQueueAnalyticsRows(['action' => ['=', $action1], 'business_id' => ['=', $business_id ], 'date' => ['>=', $startdate], 'date.' => ['<=', $enddate]]);
        $action2_numbers = Analytics::getQueueAnalyticsRows(['action' => ['=', $action2], 'business_id' => ['=', $business_id ], 'date' => ['>=', $startdate], 'date.' => ['<=', $enddate]]);
        return Analytics::getAverageTimeFromActionArray($action1_numbers, $action2_numbers);
    }
    public static function getAverageTimeFromActionArray($action1_numbers, $action2_numbers){
        $counter = 0;
        $time_sum = 0;
        foreach($action1_numbers as $action1_number){
            foreach($action2_numbers as $action2_number){
                if($action1_number->transaction_number == $action2_number->transaction_number){
                    $counter++;
                    $time_sum += ($action2_number->action_time - $action1_number->action_time);
                    break 1;
                }
            }
        }
        $average = $counter == 0 ? 0 : round($time_sum/$counter);
        return $average;
    }
    public static function getWaitingTime($business_id){
        $date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $numbers_in_queue = Analytics::getBusinessRemainingCount($business_id);
        $average_waiting_time = Analytics::getAverageTimeCalledByBusinessId($business_id, 'numeric', $date, $date);
        return $average_waiting_time * $numbers_in_queue;
    }
    public static function getWaitingTimeString($business_id){
        $waiting_time = Analytics::getWaitingTime($business_id);
        $waiting_time = floor($waiting_time / 60);
        //Reduced to 3 different line statuses
        if($waiting_time > 30){
            $waiting_time_string = 'heavy';
        }else if($waiting_time <= 30 && $waiting_time > 15){
            $waiting_time_string = 'moderate';
        }else{
            $waiting_time_string = 'light';
        }
        return $waiting_time_string;
    }
    public static function getLastActive($business_id){
        $last = Analytics::orderBy('transaction_number', 'desc')->where('business_id', '=', $business_id)->first();
        if($last){
            $last_active = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - $last->date;
            $last_active = $last_active / 86400; //convert seconds to days
        }else{
            $last_active = null;
        }
        return $last_active;
    }
}