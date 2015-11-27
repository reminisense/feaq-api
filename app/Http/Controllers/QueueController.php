<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 19/11/15
 * Time: 1:31 PM
 */

namespace App\Http\Controllers;

use App\Models\PriorityQueue;
use App\Models\QueueSettings;
use App\Models\PriorityNumber;
use App\Models\Service;
use App\Models\TerminalTransaction;
use App\Models\Terminal;
use Illuminate\Support\Facades\Input;

class QueueController extends Controller {

  /**
   * @api {post} queue/insert-specific Inserts Specific Number
   * @apiName PostInsertSpecific
   * @apiGroup Queue
   * @apiVersion 1.0.0
   * @apiExample {js} Example Usage:
   *     https://api.featherq.com/queue/insert-specific
   * @apiDescription This function enables the authorized user to queue by inserting the validated number to the database.
   *
   * @apiHeader {String} access-key The unique access key sent by the client.
   * @apiPermission Authenticated User
   *
   * @apiParam {Number} service_id The id of the service to queue.
   * @apiParam {Number} terminal_id The id of the terminal to queue.
   * @apiParam {String="web","remote","android","specific"} queue_platform The platform where the queue is requested. <code>Web</code> is generated from the web app. <code>Remote</code> is from remote queueing-web app. <code>Android</code> is from remote queueing-Android app. <code>Specific</code> is from the process queue-issue specific number.
   * @apiParam {String} priority_number The number issued to the user.
   * @apiParam {String} name The full name of the user that is queuing.
   * @apiParam {String} phone The contact number of the user that is queuing.
   * @apiParam {String} email The email address of the user that is queuing.
   * @apiParam {String} date The timestamp format (<code>mktime(0, 0, 0, date('m'), date('d'), date('Y'))</code>) of the date the queue is requested.
   * @apiParam {Number} user_id The id of the user requesting the queue.
   * @apiParam {String} [time_assigned] The time (time()) on which the queue was inserted to the database.
   *
   * @apiSuccess (200) {Number} success The boolean flag of the successful process.
   * @apiSuccess (200) {Number} transaction_number The id of the current transaction.
   * @apiSuccess (200) {String} priority_number The number given to the user.
   * @apiSuccess (200) {String} confirmation_code The code given to the user along with the priority number for validation.
   * @apiSuccessExample {Json} Success-Response:
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *          "success": 1
   *       },
   *       {
   *         "transaction_number": 73123122,
   *         "priority_number": "21",
   *         "confirmation_code": 1GHB3JS987
   *       }
   *     ]
   *
   * @apiError (200) {String} InvalidTransaction The transaction is invalid.
   * @apiError (200) {String} InvalidMember The terminal id is not owned of the service.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *          "success": 0
   *       },
   *       {
   *         "err_code": "InvalidTransaction"
   *       },
   *     ]
   */
  public function postInsertSpecific(){
    $service_id = Input::get('service_id');
    $terminal_id = Input::get('terminal_id');
    $queue_platform = Input::get('queue_platform');
    $priority_number = Input::get('priority_number');
    $name = Input::get('name');
    $phone = Input::get('phone');
    $email = Input::get('email');
    $date = Input::get('date');
    $user_id = Input::get('user_id');
    $time_assigned = Input::get('time_assigned');
      if($service_id == Terminal::serviceId($terminal_id)) {
          //$number = ProcessQueue::issueNumber($service_id, $priority_number, null, $queue_platform, $terminal_id);
          $number = $this->issueNumber($service_id, $priority_number, $date, $queue_platform, $terminal_id, $user_id);
          if ($number) {
              PriorityQueue::updatePriorityQueueUser($number['transaction_number'], $name, $phone, $email);
              TerminalTransaction::where('transaction_number', '=', $number['transaction_number'])
                  ->update(['time_assigned' => $time_assigned]);
              return json_encode(['success' => 1, 'number' => $number]);
          }
          else {
              return json_encode(['success' => 0, 'err_code' => "InvalidTransaction"]);
          }
      }else{
          return json_encode(['success' => 0, 'err_code' => "InvalidMember"]);
      }
  }

  private function issueNumber($service_id, $priority_number = null, $date = null, $queue_platform = 'web', $terminal_id = 0, $user_id = null){
    $service_properties = $this->getServiceProperties($service_id, $date);
    $number_start = $service_properties->number_start;
    $number_limit = $service_properties->number_limit;
    $last_number_given = $service_properties->last_number_given;
    $current_number = $service_properties->current_number;
    $time_queued = time();
    $priority_number = $this->generatePriorityNumber($priority_number, $service_properties->next_number);
    $track_id = PriorityNumber::createPriorityNumber($service_id, $number_start, $number_limit, $last_number_given, $current_number, $date);
    $confirmation_code = $this->confirmationCode($track_id);
    $transaction_number = PriorityQueue::createPriorityQueue($track_id, $priority_number, $confirmation_code, $user_id, $queue_platform);
    TerminalTransaction::createTerminalTransaction($transaction_number, $time_queued, $terminal_id);
    //Analytics::insertAnalyticsQueueNumberIssued($transaction_number, $service_id, $date, $time_queued, $terminal_id, $queue_platform); //insert to queue_analytics
    $number = array(
      'transaction_number' => $transaction_number,
      'priority_number' => $priority_number,
      'confirmation_code' => $confirmation_code,
    );
    return $number;
  }

  private function generatePriorityNumber($priority_number, $next_number) {
    if(!$priority_number){
      return $next_number;
    }
    return $priority_number;
  }

  private function confirmationCode($track_id = 0) {
    return strtoupper(substr(md5($track_id), 0, 4));
  }

  private function getServiceProperties($service_id, $date = null){
    $properties = new \stdClass();
    $properties->number_start = QueueSettings::numberStart($service_id, $date);
    $properties->number_limit = QueueSettings::numberLimit($service_id, $date);
    $properties->last_number_given = $this->lastNumberGiven($service_id, $date);
    $properties->current_number = $this->currentNumber($service_id, $date);
    $properties->next_number = $this->nextNumber($properties->last_number_given, $properties->number_start, $properties->number_limit);
    return $properties;
  }

  private function nextNumber($last_number_given, $number_start, $number_limit){
    return ($last_number_given < $number_limit && $last_number_given != 0) ? $last_number_given + 1 : $number_start;
  }

  private function currentNumber($service_id, $date = null, $default = 0){
    $numbers = $this->allNumbers($service_id, null, $date);
    return $numbers ? $numbers->current_number : $default;
  }

  private function lastNumberGiven($service_id, $date = null, $default = 0){
    $numbers = $this->allNumbers($service_id, null, $date);
    return $numbers ? $numbers->last_number_given : $default;
  }

  private function allNumbers($service_id, $terminal_id = null, $date = null){
    $numbers = PriorityQueue::queuedNumbers($service_id, $date);
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
            $terminal = Terminal::where("terminal_id","=", $number->terminal_id)->first();
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

      usort($processed_numbers, array($this, 'sortProcessedNumbers'));
      usort($called_numbers, array($this, 'sortCalledNumbers'));

      $priority_numbers->last_number_given = $last_number_given;
      $priority_numbers->next_number = $this->nextNumber($priority_numbers->last_number_given, QueueSettings::numberStart($service_id), QueueSettings::numberLimit($service_id));
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

  private function sortProcessedNumbers($var1, $var2)
  {
    return $var1['time_processed'] - $var2['time_processed'];
  }

  private function sortCalledNumbers($var1, $var2)
  {
    return $var2['time_called'] - $var1['time_called'];
  }

}