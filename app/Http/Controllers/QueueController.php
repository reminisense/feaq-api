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
     * @api {get} queue/numbers/{terminal_id} Get Terminal Numbers
     * @apiName GetTerminalNumbers
     * @apiGroup Queue
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/queue/numbers/1
     * @apiDescription This function gets all the called, served, dropped, and issued numbers that are relevant to te terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiSuccess (200) {Number} success The boolean flag of the successful process.
     * @apiSuccess (200) {Object} numbers Contains information about the queue numbers.
     * @apiSuccess (200) {Number} numbers.last_number_given The last number given to a person on the queue.
     * @apiSuccess (200) {Number} numbers.next_number The next number in the queue to be called.
     * @apiSuccess (200) {Number} numbers.current_number The current number being called in the queue.
     * @apiSuccess (200) {Number} numbers.number_limit The maximum priority number to be issued to the queue.
     *
     * @apiSuccess (200) {Object[]} numbers.called_numbers The numbers that are already called in the queue.
     * @apiSuccess (200) {Object} numbers.called_number.number The information about the priority number.
     * @apiSuccess (200) {Number} numbers.called_number.number.transaction_number The transaction number of the queued number.
     * @apiSuccess (200) {Number} numbers.called_number.number.priority_number The number representation of the queued number.
     * @apiSuccess (200) {String} numbers.called_number.number.confirmation_code The encryted code representation of the queued number.
     * @apiSuccess (200) {Number} numbers.called_number.number.terminal_id The id of the calling terminal.
     * @apiSuccess (200) {String} numbers.called_number.number.terminal_name The name of the calling terminal.
     * @apiSuccess (200) {Number} numbers.called_number.number.time_called The time (<code>time()</code>) on which the number was called.
     * @apiSuccess (200) {String} numbers.called_number.number.name The name of the user assigned to that number.
     * @apiSuccess (200) {String} numbers.called_number.number.phone The mobile phone number of the user assigned to that number.
     * @apiSuccess (200) {String} numbers.called_number.number.email The email address of the user assigned to that number.
     * @apiSuccess (200) {Number} numbers.called_number.number.box_rank The ranking in which the number will be view in the broadcast screen.
     *
     * @apiSuccess (200) {Object[]} numbers.uncalled_numbers The numbers that are still to be called in the queue.
     * @apiSuccess (200) {Object} numbers.uncalled_number.number The information about the priority number.
     * @apiSuccess (200) {Number} numbers.uncalled_number.number.transaction_number The transaction number of the queued number.
     * @apiSuccess (200) {Number} numbers.uncalled_number.number.priority_number The number representation of the queued number.
     * @apiSuccess (200) {String} numbers.uncalled_number.number.name The name of the user assigned to that number.
     * @apiSuccess (200) {String} numbers.uncalled_number.number.phone The mobile phone number of the user assigned to that number.
     * @apiSuccess (200) {String} numbers.uncalled_number.number.email The email address of the user assigned to that number.
     *
     * @apiSuccess (200) {Object[]} numbers.processed_numbers The numbers that already served/removed from the queue.
     * @apiSuccess (200) {Object} numbers.processed_number.number The information about the priority number.
     * @apiSuccess (200) {Number} numbers.processed_number.number.transaction_number The transaction number of the queued number.
     * @apiSuccess (200) {Number} numbers.processed_number.number.priority_number The number representation of the queued number.
     * @apiSuccess (200) {String} numbers.processed_number.number.confirmation_code The encryted code representation of the queued number.
     * @apiSuccess (200) {Number} numbers.processed_number.number.terminal_id The id of the calling terminal.
     * @apiSuccess (200) {String} numbers.processed_number.number.terminal_name The name of the calling terminal.
     * @apiSuccess (200) {Number} numbers.processed_number.number.time_processed The time (<code>time()</code>) on which the number was processed.
     *
     * @apiSuccess (200) {Object[]} numbers.timebound_numbers The numbers that are not yet called but have specific times to be called in the queue
     * @apiSuccess (200) {Object} numbers.timebound_number.number The information about the priority number.
     * @apiSuccess (200) {Number} numbers.timebound_number.number.transaction_number The transaction number of the queued number.
     * @apiSuccess (200) {Number} numbers.timebound_number.number.priority_number The number representation of the queued number.
     * @apiSuccess (200) {String} numbers.timebound_number.number.name The name of the user assigned to that number.
     * @apiSuccess (200) {String} numbers.timebound_number.number.phone The mobile phone number of the user assigned to that number.
     * @apiSuccess (200) {String} numbers.timebound_number.number.email The email address of the user assigned to that number.
     * @apiSuccess (200) {Number} numbers.timebound_number.number.time_assigned The time (<code>time()</code>) on which the number is set to be called.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "success": 1
     *          "numbers":
     *              {
     *                  "last_number_given": "20",
     *                  "next_number": 21,
     *                  "current_number": 0,
     *                  "number_limit": 9999,
     *                  "called_numbers":
     *                      [
     *                          {
     *                              "transaction_number": 10332,
     *                              "priority_number": "16",
     *                              "confirmation_code": "4E96",
     *                              "terminal_id": 462,
     *                              "terminal_name": "Monster Terminal 1",
     *                              "time_called": 1449645142,
     *                              "name": null,
     *                              "phone": null,
     *                              "email": null,
     *                              "box_rank": 1
     *                          },
     *                      ],
     *                  "uncalled_numbers":
     *                      [
     *                          {
     *                              "transaction_number": 10332,
     *                              "priority_number": "16",
     *                              "name": null,
     *                              "phone": null,
     *                              "email": null
     *                          },
     *                      ],
     *                  "processed_numbers":
     *                      [
     *                          {
     *                              "transaction_number": 10331,
     *                              "priority_number": "15",
     *                              "confirmation_code": "B974",
     *                              "terminal_id": 462,
     *                              "terminal_name": "Monster Terminal 1",
     *                              "time_processed": 1449643314,
     *                              "status": "Served"
     *                          },
     *                      ],
     *                  "timebound_numbers":
     *                      [
     *                          {
     *                              "transaction_number": 10332,
     *                              "priority_number": "16",
     *                              "time_assigned": 1449645142,
     *                              "name": null,
     *                              "phone": null,
     *                              "email": null,
     *                              "box_rank": 1
     *                          },
     *                      ]
     *                  }
     *              }
     *          }
     *     ]
     *
     * @apiError (Error) {String} error The error message.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "error": "Terminal does not exist."
     *       },
     *     ]
     */
    public function getAllNumbers($terminal_id){
        $numbers = Queue::terminalNumbers($terminal_id);
        return json_encode(['success' => 1, 'numbers' => $numbers], JSON_PRETTY_PRINT);
    }

    /**
     * @api {put} queue/call Call Number
     * @apiName PutCallNumber
     * @apiGroup Queue
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/queue/call
     * @apiDescription This function tags a priority number as called and will be updated to the broadcast screen.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiParam {Number} terminal_id The id of the terminal calling the number.
     * @apiParam {Number} transaction_number The transaction number of the number being called.
     *
     * @apiSuccess (200) {Number} success The boolean flag of the successful process.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "success": 1
     *       }
     *     ]
     *
     * @apiError (Error) {String} error The error message.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "error": "Number 1 has already been called. Please call another number."
     *       },
     *     ]
     */
    public function putCallNumber(){
        $transaction_number = Input::get('transaction_number');
        $terminal_id = Input::get('terminal_id');
        return Queue::callNumber($transaction_number, $terminal_id);
    }

    /**
     * @api {put} queue/serve Serve Number
     * @apiName PutServeNumber
     * @apiGroup Queue
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/queue/serve
     * @apiDescription This function tags a priority number as served and will be removed from the broadcast screen.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiParam {Number} transaction_number The transaction number of the number being served.
     *
     * @apiSuccess (200) {Number} success The boolean flag of the successful process.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "success": 1
     *       }
     *     ]
     *
     * @apiError (Error) {String} error The error message.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "error": "Number 1 has already been processed. If the number still exists, please reload the page."
     *       },
     *     ]
     */
    public function putServeNumber(){
        $transaction_number = Input::get('transaction_number');
        return Queue::serveNumber($transaction_number);
    }

    /**
     * @api {put} queue/drop Drop Number
     * @apiName PutDropNumber
     * @apiGroup Queue
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/queue/drop
     * @apiDescription This function tags a priority number as dropped and will be removed from the broadcast screen.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiParam {Number} transaction_number The transaction number of the number being dropped.
     *
     * @apiSuccess (200) {Number} success The boolean flag of the successful process.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "success": 1
     *       }
     *     ]
     *
     * @apiError (Error) {String} error The error message.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "error": "Number 1 has already been processed. If the number still exists, please reload the page."
     *       },
     *     ]
     */
    public function putDropNumber(){
        $transaction_number = Input::get('transaction_number');
        return Queue::dropNumber($transaction_number);
    }


    /**
     * @api {post} queue/insert-multiple Insert Multiple Numbers
     * @apiName PostInsertMultiple
     * @apiGroup Queue
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/queue/insert-multiple
     * @apiDescription This function issues multiple numbers to a service depending on user's specifications
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiParam {Number} service_id The id of the service to queue.
     * @apiParam {Number} terminal_id The id of the terminal to queue.
     * @apiParam {Number} number_start The first number of the range to be issued.
     * @apiParam {Number} range The range of how many numbers are to be issued.
     * @apiParam {String} [date] The timestamp format (<code>mktime(0, 0, 0, date('m'), date('d'), date('Y'))</code>) of the date the queue is requested.
     *
     * @apiSuccess (200) {Number} success The boolean flag of the successful process.
     * @apiSuccess (200) {Number} first_number The first number issued in the process.
     * @apiSuccess (200) {Number} last_number The the last number issued after the process.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "success": 1,
     *          "first_number": 1,
     *          "last_number": 10
     *       }
     *     ]
     *
     * @apiError (Error) {String} error The error message.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "error": "You have missing parameters."
     *       },
     *     ]
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
     * @api {post} queue/user/rating Rate User
     * @apiName PostRateUser
     * @apiGroup Queue
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/queue/user/rating
     * @apiDescription This function gives a rating to the user the queued to the terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiParam {String} email The email address of the user to be rated.
     * @apiParam {Number} terminal_id The id of the terminal rating the user.
     * @apiParam {Number} rating The rating of user given by the terminal.
     * @apiParam {Number} action The action done while rating the user (2 = serve, 3 = drop).
     *
     * @apiSuccess (200) {Number} success The boolean flag of the successful process.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "success": 1,
     *       }
     *     ]
     *
     * @apiError (Error) {String} error The error message.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "error": "You have missing parameters."
     *       },
     *     ]
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