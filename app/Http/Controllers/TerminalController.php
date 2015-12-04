<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 12/4/2015
 * Time: 11:28 AM
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use App\Models\Business;
use App\Models\Terminal;
use App\Models\TerminalTransaction;

class TerminalController extends Controller{

    /**
     * @api {post} /terminals Create Terminal
     * @apiName Create Terminal
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
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "business":
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the terminal creation fails.
     * @apiError (Error) {String} error_message Gives the reason for the terminal creation failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Terminal name already exist."
     *      }
     *
     */
    public function postCreateTerminal(){
        $business_id = Input::get('business_id');
        $service_id = Input::get('service_id');
        $name = Input::get('name');
        if (Terminal::validateTerminalName($business_id, $name, 0)) {
            Terminal::createTerminal($service_id, $name);
            $business = Business::getBusinessDetails($business_id);
            return json_encode(['success' => 1, 'business' => $business]);
        }
        else {
            return json_encode(['status' => 0, 'error_message' => 'Terminal name already exists.']);
        }
    }

    /**
     * @api {delete} /terminals/{id} Delete Terminal
     * @apiName Delete Terminal
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/1
     * @apiDescription Deletes a specified terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam none
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
     * @apiError (Error) {String} error_message Gives the reason for the terminal deletion failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "There are still pending numbers for this terminal."
     *      }
     *
     */
    public function deleteRemoveTerminal($terminal_id){
        $business_id = Business::getBusinessIdByTerminalId($terminal_id);
        $error = 'There are still pending numbers for this terminal.';
        if (TerminalTransaction::terminalActiveNumbers($terminal_id) == 0) {
            Terminal::deleteTerminal($terminal_id);
            $error = NULL;
        }
        $business = Business::getBusinessDetails($business_id);
        $business['error'] = $error;
        return json_encode(['success' => 1, 'business' => $business]);
    }

    /**
     * @api {put} /terminals/{id} Update Terminal
     * @apiName Update Terminal
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/1
     * @apiDescription Updates the name of a terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
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
     * @apiError (Error) {String} error_message Gives the reason for the terminal update failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Terminal name already exist."
     *      }
     *
     */
    public function putUpdateTerminalName($terminal_id){
        $name = Input::get('name');
        $business_id = Business::getBusinessIdByTerminalId($terminal_id);

        if (Terminal::validateTerminalName($business_id, $name, $terminal_id)) {
            Terminal::setName($terminal_id, $name);
            return json_encode(['success' => 1]);
        }
        else {
            return json_encode(['success' => 0, 'error_message' => 'Terminal name already exists.']);
        }
    }

    /**
     * @api {post} /terminals/user Add Terminal User
     * @apiName Add Terminal User
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
     * @apiError (Error) {String} error_message Gives the reason for the user assignment failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Terminal does not exist."
     *      }
     *
     */
    public function postAddUser(){
        $terminal_id = Input::get('terminal_id');
        $user_id = Input::get('user_id');
        if(!Terminal::where('terminal_id', '=', $terminal_id)->exists()) {
            return json_encode(['success' => 0, 'error_message' => 'Terminal does not exist.']);
        }elseif(!Terminal::where('user_id', '=', $user_id)->exists()){
            $business_id = Business::getBusinessIdByTerminalId($terminal_id);
            $business = Business::getBusinessDetails($business_id);
            $business['error'] = 'User does not exist.';
            return json_encode(['success' => 1, 'business' => $business]);
        }else{
            $business_id = Business::getBusinessIdByTerminalId($terminal_id);
            TerminalUser::assignTerminalUser($user_id, $terminal_id);
            $business = Business::getBusinessDetails($business_id);
            return json_encode(['success' => 1, 'business' => $business]);
        }
    }

    /**
     * @api {delete} /terminals/user/{terminal_id}/{user_id} Remove Terminal User
     * @apiName Remove Terminal User
     * @apiGroup Terminal
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/terminals/user
     * @apiDescription Removes a user as a terminal user from a specified terminal.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
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
     * @apiError (Error) {String} error_message Gives the reason for the user removal failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Terminal does not exist."
     *      }
     *
     */
    public function deleteRemoveUser($terminal_id, $user_id){
        if(!Terminal::where('terminal_id', '=', $terminal_id)->exists()) {
            return json_encode(['success' => 1, 'error_message' => 'Terminal does not exist.']);
        }elseif(!Terminal::where('user_id', '=', $user_id)->exists()){
            $business_id = Business::getBusinessIdByTerminalId($terminal_id);
            $business = Business::getBusinessDetails($business_id);
            $business['error'] = 'User does not exist.';
            return json_encode(['success' => 1, 'business' => $business]);
        }else {
            $business_id = Business::getBusinessIdByTerminalId($terminal_id);
            TerminalUser::unassignTerminalUser($user_id, $terminal_id);
            $business = Business::getBusinessDetails($business_id);
            return json_encode(['success' => 1, 'business' => $business]);
        }
    }

}