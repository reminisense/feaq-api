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
     * @api {post} /login
     * @apiName login
     * @apiGroup Authentication
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/login
     * @apiDescription Logs in a user into the api and returns an access key
     *
     * @apiHeader none
     * @apiPermission none
     *
     * @apiParam {String} fb_id User's facebook id provided by the Facebook javascript api
     *
     * @apiSuccess (Success 200) {String} success The status of the request
     * @apiSuccess (Success 200) {String} accessToken The accessKey to remember the session
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "accessToken": "123123drink123123drink"
     *      }
     *
     * @apiError (Error 404) {String} success Returns 0 success if failed
     * @apiError (Error 404) {String} error The body of the error message
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 404 Not Found
     *      {
     *          "success": 0,
     *          "error": "Please sign up to Featherq"
     *      }
     *
     */
    public function login(){
        return Authentication::login(Input::get('fb_id'));
    }

    /**
     * @api {get} /logout
     * @apiName logout
     * @apiGroup Authentication
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/logout
     * @apiDescription Logs out a user from the api
     *
     * @apiHeader none
     * @apiPermission none
     *
     * @apiSuccess (Success 200) {String} success The status of the request
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *      }
     *
     */
    public function logout(){
        return Authentication::logout();
    }

    /**
     * @api {post} /user/register
     * @apiName register
     * @apiGroup Authentication
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/user/register
     * @apiDescription Registers a new user to the database
     *
     * @apiHeader none
     * @apiPermission none
     *
     * @apiParam {String} accessToken The facebook access token (provided by the Facebook javascript api)
     * @apiParam {String} fb_id User's facebook id (provided by the Facebook javascript api)
     * @apiParam {String} fb_url User's facebook url (provided by the Facebook javascript api)
     * @apiParam {String} first_name User's first name (provided by the Facebook javascript api)
     * @apiParam {String} last_name User's last name (provided by the Facebook javascript api)
     * @apiParam {String} email User's email (provided by the Facebook javascript api)
     * @apiParam {String} gender User's gender (provided by the Facebook javascript api)
     *
     * @apiSuccess (Success 200) {String} success The status of the request
     * @apiSuccess (Success 200) {String} accessToken The accessKey to remember the session
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "accessToken": "123123drink123123drink"
     *      }
     *
     * @apiError (Error 404) {String} success Returns 0 success if failed
     * @apiError (Error 404) {String} error The body of the error message
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 404 Not Found
     *      {
     *          "success": 0,
     *          "error": "Please sign up to Featherq"
     *      }
     *
     */
    public function register(){
        return Authentication::register(Input::all());
    }
}