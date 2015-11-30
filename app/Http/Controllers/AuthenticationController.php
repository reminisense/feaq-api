<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 3:35 PM
 */

namespace App\Http\Controllers;

use App\Models\Authentication;
use Illuminate\Support\Facades\Input;

class AuthenticationController extends Controller
{

    /**
     * @api {post} /login User Login
     * @apiName Login
     * @apiGroup Authentication
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/login
     * @apiDescription Checks for the user in the database and returns an access key.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} fb_id User's Facebook id provided by the Facebook javascript API.
     * @apiParam {String} [click_source] The location of the button (e.g. <code>landing_page_top_right</code>), where the user logged in.
     *
     * @apiSuccess (200) {Number} success Returns <code>1</code> if the login is successful.
     * @apiSuccess (200) {String} accessToken The access key to remember the session.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "accessToken": "123123drink123123drink"
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the login fails.
     * @apiError (Error) {String} MissingValue Missing <code>fb_id</code> upon request.
     * @apiError (Error) {String} SignUpRequired User with the given <code>fb_id</code> has not yet registered to Featherq.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "SignUpRequired"
     *      }
     *
     */
    public function login(){
        return Authentication::login(Input::get('fb_id'), Input::get('click_source'));
    }

    /**
     * @api {get} /logout User Logout
     * @apiName Logout
     * @apiGroup Authentication
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/logout
     * @apiDescription Forgets the user's session from the app.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiSuccess (200) {Number} success Returns <code>1</code> if the logout is successful.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     */
    public function logout(){
        return Authentication::logout();
    }


    /**
     * @api {post} /user/register User Registration
     * @apiName Register
     * @apiGroup Authentication
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/user/register
     * @apiDescription Registers a new user to the database.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {String} accessToken The facebook access token (provided by the Facebook javascript API).
     * @apiParam {Number} fb_id User's facebook id (provided by the Facebook javascript API).
     * @apiParam {String} fb_url User's facebook url (provided by the Facebook javascript API).
     * @apiParam {String} first_name User's first name (provided by the Facebook javascript API).
     * @apiParam {String} last_name User's last name (provided by the Facebook javascript API).
     * @apiParam {String} email User's email (provided by the Facebook javascript API).
     * @apiParam {String} gender User's gender (provided by the Facebook javascript API).
     * @apiParam {String} [click_source] The location of the button (e.g. <code>landing_page_top_right</code>), where the user logged in to Featherq.
     *
     * @apiSuccess (200) {Number} success Returns <code>1</code> if the sign up is successful.
     * @apiSuccess (200) {String} accessToken The access key to remember the session.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "accessToken": "123123drink123123drink"
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if registration fails.
     * @apiError (Error) {String} AuthenticationFailed Invalid <code>accessToken</code> or <code>fb_id</code>.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "AuthenticationFailed"
     *      }
     *
     */
    public function register(){
        return Authentication::register(Input::all());
    }
}