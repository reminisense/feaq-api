<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 12/4/2015
 * Time: 11:28 AM
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use App\Models\User;
use App\Models\Business;
use App\Models\Terminal;
use App\Models\TerminalTransaction;
use App\Models\TerminalUser;

class TerminalController extends Controller{

    /**
     * @api {post} /terminals Create Terminal
     * @apiName CreateTerminal
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals
     * @apiDescription Creates a new terminal for a business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} business_id The <code>business_id</code> of the business where the created terminal is under.
     * @apiParam {Number} service_id The <code>service_id</code> of the service where the created terminal is under.
     * @apiParam {String} name The name of the terminal to be created.
     *
     * @apiSuccess (200) {Number} success Returns the <code>1</code> if terminal creation is successful.
     * @apiSuccess (200) {Object} business The details of a business
     * @apiSuccess (200) {Number} business.business_id The id of the business
     * @apiSuccess (200) {String} business.business_name The name of the business
     * @apiSuccess (200) {String} business.business_address The address of the business
     * @apiSuccess (200) {String} business.facebook_url The facebook url of the business
     * @apiSuccess (200) {String} business.industry  The industry of the business
     * @apiSuccess (200) {String} business.time_open The time when the business opens
     * @apiSuccess (200) {String} business.time_closed The time when the business closes
     * @apiSuccess (200) {String} business.timezone The php timezone of the business
     * @apiSuccess (200) {Number} business.queue_limit TThe maximum priority number to be issued to the queue
     * @apiSuccess (200) {Number} business.terminal_specific_issue The setting which determines if a terminal user can view only the number he/she has issued
     * @apiSuccess (200) {Number} business.sms_current_number The setting which determines if the system notifies the current number being called
     * @apiSuccess (200) {Number} business.sms_1_ahead The setting which determines if the system notifies the person 1 number ahead of the current number being called
     * @apiSuccess (200) {Number} business.sms_5_ahead The setting which determines if the system notifies the person 5 numbers ahead of the current number being called
     * @apiSuccess (200) {Number} business.sms_10_ahead The setting which determines if the system notifies the person 10 numbers ahead of the current number being called
     * @apiSuccess (200) {Number} business.sms_blank_ahead The setting which determines if the system notifies the person n numbers ahead of the current number being called based on the input_sms_field
     * @apiSuccess (200) {Number} business.input_sms_field The number set which notifies which number is to be notified after each number call
     * @apiSuccess (200) {Number} business.allow_remote The setting which determines if the business allows remote queueing
     * @apiSuccess (200) {Number} business.remote_limit The setting which determines how many users are allowed to queue remotely
     * @apiSuccess (200) {String} business.sms_gateway The setting which determines which SMS gateway will be used for sms notifications
     * @apiSuccess (200) {String} business.raw_code The unique raw code of the business

     * @apiSuccess (200) {Object) business.analytics The queueing analytics numbers of the business
     * @apiSuccess (200) {Number) business.analytics.remaining_count The remaining numbers in queue for the business
     * @apiSuccess (200) {Number) business.analytics.total_numbers_issued The total number of issued numbers for the business
     * @apiSuccess (200) {Number) business.analytics.total_numbers_called The total number of called numbers for the business
     * @apiSuccess (200) {Number) business.analytics.total_numbers_served The total number of served numbers for the business
     * @apiSuccess (200) {Number) business.analytics.total_numbers_dropped The total number of dropped numbers for the business
     * @apiSuccess (200) {String) business.analytics.average_time_called The average time in calling numbers
     * @apiSuccess (200) {Number) business.analytics.average_time_served The average time in serving numbers
     *
     * @apiSuccess (200) {Object} business.features The business features allowed by the administrator
     * @apiSuccess (200) {Number} business.features.terminal_users The number of terminal users allowed for the business
     * @apiSuccess (200) {Boolean} business.features.allow_sms This determines if administrator allows the business to send sms notifications
     * @apiSuccess (200) {Boolean} business.features.queue_forwarding This determines if administrator allows the business to forward their priority numbers to another business
     *
     * @apiSuccess (200) {Object[]} business.services The list of services of the business
     * @apiSuccess (200) {Object} business.services.service The details of the service
     * @apiSuccess (200) {Number} business.services.service.service_id The id of the service
     * @apiSuccess (200) {Number} business.services.service.branch_id The id of the branch where the service is located
     * @apiSuccess (200) {Number} business.services.service.business_id The id of the business where the service is located
     * @apiSuccess (200) {String} business.services.service.name The name of the service
     * @apiSuccess (200) {Object[]} business.services.service.terminals The terminals of the service
     * @apiSuccess (200) {Object} business.services.service.terminals.terminal The details of the terminal
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.terminal_id The id of the terminal
     * @apiSuccess (200) {String} business.services.service.terminals.terminal.name The name of the terminal
     * @apiSuccess (200) {String} business.services.service.terminals.terminal.code The code assigned to the terminal
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.service_id The id of the service where the terminal is located
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.status The status of the terminal
     * @apiSuccess (200) {String} business.services.service.terminals.terminal.time_created The date and time when the terminal was created
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.box_rank The rank of the terminal which determines its box color in the broadcast page
     * @apiSuccess (200) {Object[]} business.services.service.terminals.terminal.users The terminal users of the terminal
     * @apiSuccess (200) {Object} business.services.service.terminals.terminal.users.user The details of the terminal user
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.users.user.terminal_user_id The id of the terminal user in the terminal user table
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.users.user.user_id The id of the user in the user table
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.users.user.terminal_id The id of the terminal
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.users.user.status The status of the terminal user
     * @apiSuccess (200) {Number} business.services.service.terminals.terminal.users.user.date The date in seconds of when the terminal user was added
     * @apiSuccess (200) {String} business.services.service.terminals.terminal.users.user.first_name The first name of the terminal user
     * @apiSuccess (200) {String} business.services.service.terminals.terminal.users.user.last_name The last name of the terminal user
     *
     * @apiSuccess (200) {Object[]} business.terminals The list of terminals of the business
     * @apiSuccess (200) {Object} business.terminals.terminal The details of the terminal
     * @apiSuccess (200) {Number} business.terminals.terminal.terminal_id The id of the terminal
     * @apiSuccess (200) {String} business.terminals.terminal.name The name of the terminal
     * @apiSuccess (200) {String} business.terminals.terminal.code The code assigned to the terminal
     * @apiSuccess (200) {Number} business.terminals.terminal.service_id The id of the service where the terminal is located
     * @apiSuccess (200) {Number} business.terminals.terminal.status The status of the terminal
     * @apiSuccess (200) {String} business.terminals.terminal.time_created The date and time when the terminal was created
     * @apiSuccess (200) {Number} business.terminals.terminal.box_rank The rank of the terminal which determines its box color in the broadcast page
     * @apiSuccess (200) {Object[]} business.terminals.terminal.users The terminal users of the terminal
     * @apiSuccess (200) {Object} business.terminals.terminal.users.user The details of the terminal user
     * @apiSuccess (200) {Number} business.terminals.terminal.users.user.terminal_user_id The id of the terminal user in the terminal user table
     * @apiSuccess (200) {Number} business.terminals.terminal.users.user.user_id The id of the user in the user table
     * @apiSuccess (200) {Number} business.terminals.terminal.users.user.terminal_id The id of the terminal
     * @apiSuccess (200) {Number} business.terminals.terminal.users.user.status The status of the terminal user
     * @apiSuccess (200) {Number} business.terminals.terminal.users.user.date The date in seconds of when the terminal user was added
     * @apiSuccess (200) {String} business.terminals.terminal.users.user.first_name The first name of the terminal user
     * @apiSuccess (200) {String} business.terminals.terminal.users.user.last_name The last name of the terminal user
     *
     * @apiSuccess (200) {Object[]} business.allowed_businesses The list of businesses allowed to forward numbers to the queue
     * @apiSuccess (200) {Object} business.allowed_business.business The details of the allowed business
     * @apiSuccess (200) {Object} business.allowed_business.business.business_id The id of the allowed business
     * @apiSuccess (200) {Object} business.allowed_business.business.business_name The name of the allowed business
     *
     *
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "business":
     *              {
     *                  "business_id": 1,
     *                  "business_name": "Monsters Inc",
     *                  "business_address": "Hernan Cortes",
     *                  "facebook_url": "facebook.com/monstersinc",
     *                  "industry": "Energy",
     *                  "time_open": "10:00 AM",
     *                  "time_closed": "10:00 PM",
     *                  "timezone": "Asia/Manila",
     *                  "queue_limit": 9999,
     *                  "terminal_specific_issue": 0,
     *                  "sms_current_number": 0,
     *                  "sms_1_ahead": 0,
     *                  "sms_5_ahead": 0,
     *                  "sms_10_ahead": 0,
     *                  "sms_blank_ahead": 0,
     *                  "input_sms_field": 0,
     *                  "allow_remote": 0,
     *                  "sms_gateway": 0,
     *                  "raw_code": "qp0w",
     *                  "analytics":
     *                      {
     *                          "remaining_count": 0,
     *                          "total_numbers_issued": 0,
     *                          "total_numbers_called": 0,
     *                          "total_numbers_served": 0,
     *                          "total_numbers_dropped": 0,
     *                          "average_time_called": 0,
     *                          "average_time_served": 0
     *                      },
     *                  "features":
     *                      {
     *                          "terminal_users": 5,
     *                          "allow_sms": "true",
     *                          "queue_forwarding": "true"
     *                      },
     *                  "services":
     *                      [
     *                          {
     *                              "service_id": 168,
     *                              "branch_id": 168,
     *                              "business_id": 168,
     *                              "name": "Monsters Inc Service",
     *                              "terminals":
     *                                  [
     *                                      {
     *                                          "terminal_id": 462,
     *                                          "name": "Monster Termina 1",
     *                                          "code": "",
     *                                          "service_id": 168,
     *                                          "status": 1,
     *                                          "time_created": "2015-12-03 04:32:10",
     *                                          "box_rank": 1,
     *                                          "users":
     *                                              [
     *                                                  {
     *                                                      "terminal_user_id": 503,
     *                                                      "user_id": 43,
     *                                                      "terminal_id": 462,
     *                                                      "status": 1,
     *                                                      "date": 1449072000,
     *                                                      "first_name": "Aunne Rouie",
     *                                                      "last_name": Arzadon
     *                                                  },
     *                                              ]
     *                                      }
     *                                  ]
     *                          },
     *                      ],
     *                  "terminals":
     *                      [
     *                          {
     *                              "terminal_id": 462,
     *                              "name": "Monster Termina 1",
     *                              "code": "",
     *                              "service_id": 168,
     *                              "status": 1,
     *                              "time_created": "2015-12-03 04:32:10",
     *                              "box_rank": 1,
     *                              "users":
     *                                  [
     *                                      {
     *                                          "terminal_user_id": 503,
     *                                          "user_id": 43,
     *                                          "terminal_id": 462,
     *                                          "status": 1,
     *                                          "date": 1449072000,
     *                                          "first_name": "Aunne Rouie",
     *                                          "last_name": Arzadon
     *                                      },
     *                                  ]
     *                          }
     *                      ],
     *                  "allowed_businesses":
     *                      [
     *                          {
     *                              "business_id": 1,
     *                              "business_name": "Monsters Inc"
     *                          },
     *                      ]
     *              }
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the terminal creation fails.
     * @apiError (Error) {String} err_code Gives the reason for the terminal creation failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "TerminalNameExists"
     *      }
     *
     */
    public function postCreateTerminal(){
        return Terminal::createBusinessTerminal(Input::get('business_id'), Input::get('service_id'), Input::get('name'));
    }

    /**
     * @api {delete} /terminals/{id} Delete Terminal
     * @apiName DeleteTerminal
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/1
     * @apiDescription Deletes a specified terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} terminal_id The id of the terminal to be deleted.
     *
     * @apiSuccess (200) {Number} success Returns the <code>1</code> if terminal deletion is successful.
     * @apiSuccess (200) {Object} business The details of a business
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *          "business":
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the terminal deletion fails.
     * @apiError (Error) {String} err_code Gives the reason for the terminal deletion failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "NoTerminalFound"
     *      }
     *
     */
    public function deleteRemoveTerminal($terminal_id){
        return Terminal::deleteTerminal($terminal_id);
    }

    /**
     * @api {put} /terminals/{id} Update Terminal
     * @apiName UpdateTerminal
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/1
     * @apiDescription Updates the name of a terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} terminal_id The id of the terminal to be updated.
     * @apiParam {String} name The name which the terminal should be updated to.
     *
     * @apiSuccess (200) {Number} success Returns the <code>1</code> if terminal creation is successful.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the terminal update fails.
     * @apiError (Error) {String} err_code Gives the reason for the terminal update failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "TerminalNameExists"
     *      }
     *
     */
    public function putUpdateTerminalName($terminal_id){
        return Terminal::setName($terminal_id, Input::get('name'));
    }

    /**
     * @api {post} /terminals/user Add Terminal User
     * @apiName AddTerminalUser
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/user
     * @apiDescription Assigns an existing user as a terminal user.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} terminal_id The <code>terminal_id</code> of the terminal where the user is assigned to.
     * @apiParam {Number} user_id The <code>user_id</code> of the user to be assigned to the terminal.
     *
     * @apiSuccess (200) {Number} success Returns the <code>1</code> if user assignment is successful.
     * @apiSuccess (200) {Object} business The details of a business
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *          "business":
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the user assignment fails.
     * @apiError (Error) {String} err_code Gives the reason for the user assignment failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "NoTerminalFound"
     *      }
     *
     */
    public function postAddUser(){
        return TerminalUser::assignTerminalUser(Input::get('user_id'), Input::get('terminal_id'));
    }

    /**
     * @api {delete} /terminals/user/{terminal_id}/{user_id} Remove Terminal User
     * @apiName RemoveTerminalUser
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/user
     * @apiDescription Removes a user as a terminal user from a specified terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} terminal_id The <code>terminal_id</code> of the terminal where the user is assigned to.
     * @apiParam {Number} user_id The <code>user_id</code> of the user assigned to the terminal.

     * @apiSuccess (200) {Number} success Returns the <code>1</code> if user removal is successful.
     * @apiSuccess (200) {Object} business The details of a business
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *          "business":
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the user removal fails.
     * @apiError (Error) {String} err_code Gives the reason for the user removal failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "NoTerminalFound"
     *      }
     *
     */
    public function deleteRemoveUser($terminal_id, $user_id){
        return TerminalUser::unassignTerminalUser($user_id, $terminal_id);
    }

}