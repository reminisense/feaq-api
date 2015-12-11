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
class Queue extends Model{
	public static function nextNumber($last_number_given, $number_start, $number_limit){
		return ($last_number_given < $number_limit && $last_number_given != 0) ? $last_number_given + 1 : $number_start;
	}
	public static function queuedNumbers($service_id, $date, $start = 0, $take = 2147483648){
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
	public static function allNumbers($service_id, $terminal_id = null, $date = null){
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
		if($numbers){
			foreach($numbers as $number){
				$called = $number->time_called != 0 ? TRUE : FALSE;
				$served = $number->time_completed != 0 ? TRUE : FALSE;
				$removed = $number->time_removed != 0 ? TRUE : FALSE;
				$timebound = ($number->time_assigned) != 0 && ($number->time_assigned <= time()) ? TRUE : FALSE;
				$terminal_name = '';
				if($number->terminal_id){
					try{
						$terminal = Terminal::findOrFail($number->terminal_id);
						$terminal_name = $terminal->name;
					}catch(Exception $e){
						$terminal_name = '';
					}
				}
				if($number->queue_platform != 'specific'){
					$last_number_given = $number->priority_number;
				}
				/*legend*/
				//uncalled  : not served and not removed
				//called    : called, not served and not removed
				//dropped   : called, not served but removed
				//removed   : not called but removed
				//served    : called and served
				//processed : dropped/removed/served
				if(!$called && !$removed && $timebound){
					$timebound_numbers[] = array(
						'transaction_number' => $number->transaction_number,
						'priority_number' => $number->priority_number,
						'name' => $number->name,
						'phone' => $number->phone,
						'email' => $number->email,
					);
				}else if(!$called && !$removed && $terminal_specific_calling && ($number->terminal_id == $terminal_id || $number->terminal_id == 0)){
					$uncalled_numbers[] = array(
						'transaction_number' => $number->transaction_number,
						'priority_number' => $number->priority_number,
						'name' => $number->name,
						'phone' => $number->phone,
						'email' => $number->email,
					);
				}else if(!$called && !$removed && (!$terminal_specific_calling || $terminal_id == null)){
					$uncalled_numbers[] = array(
						'transaction_number' => $number->transaction_number,
						'priority_number' => $number->priority_number,
						'name' => $number->name,
						'phone' => $number->phone,
						'email' => $number->email,
					);
				}else if($called && !$served && !$removed){
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
				}else if($called && !$served && $removed){
					$processed_numbers[] = array(
						'transaction_number' => $number->transaction_number,
						'priority_number' => $number->priority_number,
						'confirmation_code' => $number->confirmation_code,
						'terminal_id' => $number->terminal_id,
						'terminal_name' => $terminal_name,
						'time_processed' => $number->time_removed,
						'status' => 'Dropped',
					);
				}else if(!$called && $removed){
					$processed_numbers[] = array(
						'transaction_number' => $number->transaction_number,
						'priority_number' => $number->priority_number,
						'confirmation_code' => $number->confirmation_code,
						'terminal_id' => $number->terminal_id,
						'terminal_name' => $terminal_name,
						'time_processed' => $number->time_removed,
						'status' => 'Removed',
					);
				}else if($called && $served){
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
		}else{
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
}