<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 11:01 AM
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Queue extends Model
{
    public static function nextNumber($last_number_given, $number_start, $number_limit)
    {
        return ($last_number_given < $number_limit && $last_number_given != 0) ? $last_number_given + 1 : $number_start;
    }

    public static function queuedNumbers($service_id, $date, $start = 0, $take = 2147483648)
    {
        $query = DB::select('
			SELECT
				n.*,
				q.priority_number,
				q.confirmation_code,
				q.queue_platform,
				q.name,
			    q.phone,
			    q.email,
				t.transaction_number,
				t.time_called,
				t.time_removed,
				t.time_completed,
				t.time_assigned,
			    t.terminal_id
			FROM
				`priority_number` n,
				`priority_queue` q,
				`terminal_transaction` t
			WHERE
				n.date = ? AND
				n.service_id = ? AND
				q.track_id = n.track_id AND
				t.transaction_number = q.transaction_number
			GROUP BY
				n.track_id
			LIMIT ?, ?
		', [$date, $service_id, $start, $take]);
        return !empty($query) ? $query : [];
    }

    public static function terminalNumbers($terminal_id, $date = null){
        if(Terminal::where('terminal_id', '=', $terminal_id)->exists()){
            return Queue::allNumbers(Terminal::serviceId($terminal_id), $terminal_id, $date);
        }else{
            return json_encode(['error' => 'Terminal does not exist.']);
        }
    }

    public static function allNumbers($service_id, $terminal_id = null, $date = null)
    {
        $date = $date == null ? mktime(0, 0, 0, date('m'), date('d'), date('Y')) : $date;
        $numbers = Queue::queuedNumbers($service_id, $date);
        $terminal_specific_calling = QueueSettings::terminalSpecificIssue($service_id);
        $number_limit = QueueSettings::numberLimit($service_id);
        $last_number_given = 0;
        $called_numbers = array();
        $uncalled_numbers = array();
        $processed_numbers = array();
        $timebound_numbers = array(); //ARA Timebound assignment
        $priority_numbers = new \stdClass();
        if ($numbers) {
            foreach ($numbers as $number) {
                $called = $number->time_called != 0 ? TRUE : FALSE;
                $served = $number->time_completed != 0 ? TRUE : FALSE;
                $removed = $number->time_removed != 0 ? TRUE : FALSE;
                $timebound = ($number->time_assigned) != 0 && ($number->time_assigned <= time()) ? TRUE : FALSE;
                $terminal_name = '';
                if ($number->terminal_id) {
                    try {
                        $terminal = Terminal::findOrFail($number->terminal_id);
                        $terminal_name = $terminal->name;
                    } catch (Exception $e) {
                        $terminal_name = '';
                    }
                }
                if ($number->queue_platform != 'specific') {
                    $last_number_given = $number->priority_number;
                }
                /*legend*/
                //uncalled  : not served and not removed
                //called    : called, not served and not removed
                //dropped   : called, not served but removed
                //removed   : not called but removed
                //served    : called and served
                //processed : dropped/removed/served
                if (!$called && !$removed && $timebound) {
                    $timebound_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'name' => $number->name,
                        'phone' => $number->phone,
                        'email' => $number->email,
                        'time_assigned' => $number->time_assigned,
                    );
                } else if (!$called && !$removed && $terminal_specific_calling && ($number->terminal_id == $terminal_id || $number->terminal_id == 0)) {
                    $uncalled_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'name' => $number->name,
                        'phone' => $number->phone,
                        'email' => $number->email,
                    );
                } else if (!$called && !$removed && (!$terminal_specific_calling || $terminal_id == null)) {
                    $uncalled_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'name' => $number->name,
                        'phone' => $number->phone,
                        'email' => $number->email,
                    );
                } else if ($called && !$served && !$removed) {
                    $called_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'confirmation_code' => $number->confirmation_code,
                        'terminal_id' => $number->terminal_id,
                        'terminal_name' => $terminal_name,
                        'time_called' => $number->time_called,
                        'name' => $number->name,
                        'phone' => $number->phone,
                        'email' => $number->email,
                        'box_rank' => Terminal::boxRank($number->terminal_id) // Added by PAG
                    );
                } else if ($called && !$served && $removed) {
                    $processed_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'confirmation_code' => $number->confirmation_code,
                        'terminal_id' => $number->terminal_id,
                        'terminal_name' => $terminal_name,
                        'time_processed' => $number->time_removed,
                        'status' => 'Dropped',
                    );
                } else if (!$called && $removed) {
                    $processed_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'confirmation_code' => $number->confirmation_code,
                        'terminal_id' => $number->terminal_id,
                        'terminal_name' => $terminal_name,
                        'time_processed' => $number->time_removed,
                        'status' => 'Removed',
                    );
                } else if ($called && $served) {
                    $processed_numbers[] = array(
                        'transaction_number' => $number->transaction_number,
                        'priority_number' => $number->priority_number,
                        'confirmation_code' => $number->confirmation_code,
                        'terminal_id' => $number->terminal_id,
                        'terminal_name' => $terminal_name,
                        'time_processed' => $number->time_completed,
                        'status' => 'Served',
                    );
                }
            }

            //ARA sorting processed numbers and called numbers explicitly
            usort($processed_numbers, function ($var1, $var2) {
                return $var1['time_processed'] - $var2['time_processed'];
            });
            usort($called_numbers, function ($var1, $var2) {
                return $var2['time_called'] - $var1['time_called'];
            });

            $priority_numbers->last_number_given = $last_number_given;
            $priority_numbers->next_number = Queue::nextNumber($priority_numbers->last_number_given, QueueSettings::numberStart($service_id), QueueSettings::numberLimit($service_id));
            $priority_numbers->current_number = $called_numbers ? $called_numbers[key($called_numbers)]['priority_number'] : 0;
            $priority_numbers->number_limit = $number_limit;
            $priority_numbers->called_numbers = $called_numbers;
            $priority_numbers->uncalled_numbers = $uncalled_numbers;
            $priority_numbers->processed_numbers = array_reverse($processed_numbers);
            $priority_numbers->timebound_numbers = $timebound_numbers;
        } else {
            $priority_numbers->last_number_given = 0;
            $priority_numbers->next_number = QueueSettings::numberStart($service_id);
            $priority_numbers->current_number = 0;
            $priority_numbers->number_limit = $number_limit;
            $priority_numbers->called_numbers = $called_numbers;
            $priority_numbers->uncalled_numbers = $uncalled_numbers;
            $priority_numbers->processed_numbers = array_reverse($processed_numbers);
            $priority_numbers->timebound_numbers = $timebound_numbers;
        }
        return $priority_numbers;
    }


    public static function issueNumber($service_id, $priority_number = null, $date = null, $queue_platform = 'web', $terminal_id = 0, $user_id = null)
    {
        $date = $date ? $date : mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $service_properties = Queue::getServiceProperties($service_id, $date);
        $number_start = $service_properties->number_start;
        $number_limit = $service_properties->number_limit;
        $last_number_given = $service_properties->last_number_given;
        $current_number = $service_properties->current_number;
        $time_queued = time();
        $priority_number = Queue::generatePriorityNumber($priority_number, $service_properties->next_number);
        $track_id = PriorityNumber::createPriorityNumber($service_id, $number_start, $number_limit, $last_number_given, $current_number, $date);
        $confirmation_code = Queue::confirmationCode($track_id);
        $transaction_number = PriorityQueue::createPriorityQueue($track_id, $priority_number, $confirmation_code, $user_id, $queue_platform);
        TerminalTransaction::createTerminalTransaction($transaction_number, $time_queued, $terminal_id);
        Analytics::insertAnalyticsQueueNumberIssued($transaction_number, $service_id, $date, $time_queued, $terminal_id, $queue_platform); //insert to queue_analytics
        $number = array(
            'transaction_number' => $transaction_number,
            'priority_number' => $priority_number,
            'confirmation_code' => $confirmation_code,
        );
        return $number;
    }

    public static function generatePriorityNumber($priority_number, $next_number)
    {
        if (!$priority_number) {
            return $next_number;
        }
        return $priority_number;
    }

    public static function confirmationCode($track_id = 0)
    {
        return strtoupper(substr(md5($track_id), 0, 4));
    }

    public static function getServiceProperties($service_id, $date = null)
    {
        $properties = new \stdClass();
        $properties->number_start = QueueSettings::numberStart($service_id, $date);
        $properties->number_limit = QueueSettings::numberLimit($service_id, $date);
        $properties->last_number_given = Queue::lastNumberGiven($service_id, $date);
        $properties->current_number = Queue::currentNumber($service_id, $date);
        $properties->next_number = Queue::nextNumber($properties->last_number_given, $properties->number_start, $properties->number_limit);
        return $properties;
    }

    public static function currentNumber($service_id, $date = null, $default = 0)
    {
        $numbers = Queue::allNumbers($service_id, null, $date);
        return $numbers ? $numbers->current_number : $default;
    }

    public static function lastNumberGiven($service_id, $date = null, $default = 0)
    {
        $numbers = Queue::allNumbers($service_id, null, $date);
        return $numbers ? $numbers->last_number_given : $default;
    }

    public static function callNumber($transaction_number, $terminal_id){
        try{
            if(is_null(TerminalTransaction::find($transaction_number))){
                return json_encode(['error' => 'You have called an invalid input.']);
            }

            $terminal_transaction = TerminalTransaction::find($transaction_number);
            $priority_queue = PriorityQueue::find($transaction_number);
            if($terminal_transaction->time_called != 0){
                return json_encode(['error' => 'Number ' . $priority_queue->priority_number . ' has already been called. Please call another number.']);
            }else{
                return Queue::callTransactionNumber($transaction_number, $terminal_id);
            }
        }catch(\Exception $e){
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public static function serveNumber($transaction_number){
        try{
            return Queue::processNumber($transaction_number, 'serve');
        }catch(\Exception $e){
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public static function dropNumber($transaction_number){
        try{
            return Queue::processNumber($transaction_number, 'remove');
        }catch(\Exception $e){
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public static function callTransactionNumber($transaction_number, $terminal_id){
        if(is_numeric($terminal_id)){
            $pq = PriorityQueue::find($transaction_number);
            $pn = PriorityNumber::find($pq->track_id);
            $time_called = time();
            $login_id = TerminalUser::hookedTerminal($terminal_id) ? TerminalUser::getLatestLoginIdOfTerminal($terminal_id) : 0;
            TerminalTransaction::updateTransactionTimeCalled($transaction_number, $login_id, $time_called, $terminal_id);
            Analytics::insertAnalyticsQueueNumberCalled($transaction_number, $pn->service_id, $pn->date, $time_called, $terminal_id, $pq->queue_platform); //insert to queue_analytics
            //Notifier::sendNumberCalledNotification($transaction_number, $terminal_id); //notifies users that his/her number is called
            return json_encode(['success' => 1, /*'numbers' => Queue::terminalNumbers($terminal_id)*/]); //ARA removed all numbers to prevent redundant database query
        }else{
            return json_encode(['error' => 'Please assign a terminal.']);
        }
    }

    public static function processNumber($transaction_number, $process){
        $transaction = TerminalTransaction::find($transaction_number);
        $priority_queue = PriorityQueue::find($transaction_number);
        $priority_number = PriorityNumber::find($priority_queue->track_id);
        $pnumber = $priority_queue->priority_number;
        $confirmation_code = $priority_queue->confirmation_code;
        $terminal_id = $transaction->terminal_id;

        try{
            $terminal = Terminal::findOrFail($transaction->terminal_id);
            $terminal_name = $terminal->name;
        }catch(\Exception $e){
            $terminal_name = '';
        }

        //ARA in case the number was not called but served/removed which is unlikely
        if($transaction->time_called == 0 ){
            Queue::callTransactionNumber($transaction_number, $terminal_id);
        }

        if($transaction->time_removed == 0 && $transaction->time_completed == 0){
            $time = time();
            if($process == 'serve'){
                TerminalTransaction::updateTransactionTimeCompleted($transaction_number, $time);
                Analytics::insertAnalyticsQueueNumberServed($transaction_number, $priority_number->service_id, $priority_number->date, $time, $terminal_id, $priority_queue->queue_platform); //insert to queue_analytics
            }else if($process == 'remove'){
                TerminalTransaction::updateTransactionTimeRemoved($transaction_number, $time);
                Analytics::insertAnalyticsQueueNumberRemoved($transaction_number, $priority_number->service_id, $priority_number->date, $time, $terminal_id, $priority_queue->queue_platform); //insert to queue_analytics
            }
        }else{
            return json_encode(array('error' => 'Number ' . $pnumber . ' has already been processed. If the number still exists, please reload the page.'));
        }

        return json_encode(array(
            'success' => 1,
//            'priority_number' => array(
//                'transaction_number' => $transaction_number,
//                'priority_number' => $pnumber,
//                'confirmation_code' => $confirmation_code,
//                'terminal_id' => $terminal_id,
//                'terminal_name' => $terminal_name,
//            ),
//            'numbers' => Queue::allNumbers($priority_number->service_id, $terminal_id), //ARA removed all numbers to prevent redundant database query
        ));
    }

    public static function issueMultipleNumbers($data){
        if(!($data->terminal_id && $data->service_id && $data->number_start && $data->range)){return json_encode(['error' => 'MissingParameters']);}
        if(!Service::where('service_id', '=', $data->service_id)->exists()){return json_encode(['success' => 0, 'err_code' => 'NoServiceFound']);}
        if(!Terminal::where('terminal_id', '=', $data->terminal_id)->exists()) {return json_encode(['success' => 0, 'err_code' => 'NoTerminalFound']);}

        $terminal_id = QueueSettings::terminalSpecificIssue($data->service_id) ? $data->terminal_id : 0;
        $next_number = Queue::nextNumber(Queue::lastNumberGiven($data->service_id), QueueSettings::numberStart($data->service_id), QueueSettings::numberLimit($data->service_id));
        $queue_platform = $data->number_start == $next_number || $data->number_start == null ? 'web' : 'specific';
        $number_start = $data->number_start == null ? $next_number : $data->number_start;

        $result = Queue::issueMultiple($data->service_id, $number_start, $data->range, $data->date, $queue_platform, $terminal_id);
        $result['success'] = 1;
        return json_encode($result);
    }

    public static function issueMultiple($service_id, $first_number, $range, $date = null, $queue_platform = 'web', $terminal_id = 0, $user_id = null){
        $date = $date == null ? mktime(0, 0, 0, date('m'), date('d'), date('Y')) : $date;

        $service_properties = Queue::getServiceProperties($service_id, $date);
        $number_start = $service_properties->number_start;
        $number_limit = $service_properties->number_limit;
        $last_number_given = $service_properties->last_number_given;
        $current_number = $service_properties->current_number;

        $time_queued = time();
        $user_id = 0;
        $user_id = $user_id == null? Helper::userId() : $user_id;
        $priority_number = $first_number;

        $terminal_transaction_data = array();
        $analytics_data = array();
        //@todo insert bulk to priority number table and get track ids
        for($i = 1; $i <= $range; $i++){
            $track_id = PriorityNumber::createPriorityNumber($service_id, $number_start, $number_limit, $last_number_given, $current_number, $date);
            $confirmation_code = strtoupper(substr(md5($track_id), 0, 4));
            $transaction_number = PriorityQueue::createPriorityQueue($track_id, $priority_number, $confirmation_code, $user_id, $queue_platform);

            $terminal_transaction_data[] = array(
                'transaction_number' => $transaction_number,
                'time_queued' => $time_queued,
                'terminal_id' => $terminal_id
            );

            $analytics_data[] = array(
                'transaction_number' => $transaction_number,
                'date' => $date,
                'business_id' => Business::getBusinessIdByServiceId($service_id),
                'branch_id' => Service::branchId($service_id),
                'service_id' => $service_id,
                'terminal_id' => $terminal_id,
                'queue_platform' => $queue_platform,
                'user_id' => Helper::userId(),
                'action' => 0,
                'action_time' => $time_queued
            );

            $last_number_given = $priority_number;
            $priority_number++;
        }


        //@todo insert bulk to priority queue and get transaction numbers
        TerminalTransaction::insert($terminal_transaction_data); //insert bulk to terminal transaction
        Analytics::saveQueueAnalytics($analytics_data); //insert bulk to analytics
        return array('first_number' => $first_number, 'last_number' => $last_number_given);
    }

    public static function rateUser($data){
        if($data->rating && $data->email && $data->terminal_id && $data->action){
            $date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $business_id = Business::getBusinessIdByTerminalId($data->terminal_id);
            $user = User::searchByEmail($data->email);
            $user_id = $user["user_id"];
            $terminal_user_id = Helper::userId();

            UserRating::rateUser($date, $business_id, $data->rating, $user_id, $terminal_user_id, $data->action);

            return json_encode(['success' => 1]);
        }else{
            return json_encode(['error' => 'MissingParameters']);
        }

    }
}