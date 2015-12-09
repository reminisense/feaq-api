<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 19/11/15
 * Time: 1:31 PM
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Models\PriorityQueue;
use App\Models\TerminalTransaction;
use App\Models\Terminal;
use App\Models\Queue;
use App\Models\TerminalUser;

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
     * @apiParam {String} [time_assigned] The time (<code>time()</code>) on which the queue was inserted to the database.
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
     * @apiError (Error) {String} InvalidTransaction The transaction is invalid.
     * @apiError (Error) {String} InvalidMember The terminal id is not owned by the service.
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
            $number = Queue::issueNumber($service_id, $priority_number, $date, $queue_platform, $terminal_id, $user_id);
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

    /**
     * ok
     */
    public function getAllNumbers($terminal_id){
        $numbers = Queue::terminalNumbers($terminal_id);
        return json_encode(['success' => 1, 'numbers' => $numbers], JSON_PRETTY_PRINT);
    }

    /**
     * ok
     */
    public function putCallNumber(){
        $transaction_number = Input::get('transaction_number');
        $terminal_id = Input::get('terminal_id');
        return Queue::callNumber($transaction_number, $terminal_id);
    }

    /**
     * ok
     */
    public function putServeNumber(){
        $transaction_number = Input::get('transaction_number');
        return Queue::serveNumber($transaction_number);
    }

    /**
     * ok
     */
    public function putDropNumber(){
        $transaction_number = Input::get('transaction_number');
        return Queue::dropNumber($transaction_number);
    }


    /**
     * ok
     */
    public function postIssueMultiple(){
        $data = new \stdClass();
        $data->service_id = Input::get('service_id');
        $data->terminal_id = Input::get('terminal_id');
        $data->number_start = Input::get('number_start');
        $data->range = Input::get('range');
        $data->date = Input::has('date')? Input::get('date') : null;

       return Queue::issueMultipleNumbers($data);
    }

    /**
     * ok
     */
    public function postUserRating(){
        $data = new \stdClass();
        $data->rating = Input::get('rating');
        $data->email = Input::get('email');
        $data->terminal_id = Input::get('terminal_id');
        $data->action = Input::get('action');

        return Queue::rateUser($data);
    }


}