<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 1/23/15
 * Time: 4:21 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityQueue extends Model
{

    protected $table = 'priority_queue';
    protected $primaryKey = 'transaction_number';
    public $timestamps = false;

    public static function priorityNumber($transaction_number){
        return PriorityQueue::where('transaction_number', '=', $transaction_number)->first()->priority_number;
    }

    public static function name($transaction_number){
        return PriorityQueue::where('transaction_number', '=', $transaction_number)->first()->name;
    }

    public static function email($transaction_number){
        return PriorityQueue::where('transaction_number', '=', $transaction_number)->first()->email;
    }

    public static function phone($transaction_number){
        return PriorityQueue::where('transaction_number', '=', $transaction_number)->first()->phone;
    }

    public static function trackId($transaction_number){
        return PriorityQueue::where('transaction_number', '=', $transaction_number)->first()->track_id;
    }

    public static function userId($transaction_number){
        return PriorityQueue::where('transaction_number', '=', $transaction_number)->first()->user_id;
    }


    public static function createPriorityQueue($track_id, $priority_number, $confirmation_code, $user_id, $queue_platform){
        $values = [
            'priority_number' => $priority_number,
            'track_id' => $track_id,
            'confirmation_code' => $confirmation_code,
            'user_id' => $user_id,
            'queue_platform' => $queue_platform
        ];
        return PriorityQueue::insertGetId($values);
    }

    public static function updatePriorityQueueUser($transaction_number, $name = null, $phone = null, $email = null){
        $values = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ];
        PriorityQueue::where('transaction_number', '=', $transaction_number)->update($values);
    }

  public static function getTransactionNumberByTrackId($track_id) {
    return PriorityQueue::where('track_id', '=', $track_id)->select(array('transaction_number'))->get();
  }

    public static function getLatestTransactionNumberOfUser($user_id){
        return PriorityQueue::where('user_id', '=', $user_id)->orderBy('transaction_number', 'desc')->first()->transaction_number;
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

}