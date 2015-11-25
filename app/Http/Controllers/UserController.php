<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 19/11/15
 * Time: 3:57 PM
 */

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{

  /**
   * @api {get} user/{user_id} Fetch all User Details
   * @apiName FetchUserProfile
   * @apiGroup User
   * @apiVersion 1.0.0
   * @apiExample {js} Example Usage:
   *     https://api.featherq.com/user/1
   * @apiDescription Gets all the information pertaining to the user.
   *
   * @apiHeader {String} access-key The unique access key sent by the client.
   * @apiPermission Current User & Admin
   *
   * @apiParam {Number} user_id The id of the user.
   *
   * @apiSuccess (Success 200) {Number} user_id The id of the user.
   * @apiSuccess (Success 200) {String} email The email address of the user.
   * @apiSuccess (Success 200) {String} first_name The first name of the user.
   * @apiSuccess (Success 200) {String} last_name The last name of the user.
   * @apiSuccess (Success 200) {String} phone The phone number of the user.
   * @apiSuccess (Success 200) {String} local_address The address of the user.
   *
   * @apiSuccessExample {Json} Success-Response:
   *     HTTP/1.1 200 OK
   *     {
   *       "user_id": "13",
   *       "email": "foo@example.com",
   *       "first_name": "Foo Foo",
   *       "last_name": "Example",
   *       "phone": "1234567890",
   *       "local_address": "Disneyland, Hongkong"
   *     }
   *
   * @apiError (Error 404) {String} NoUserFound There were no users found with the given <code>user_id</code>.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 404 Not Found
   *     {
   *       "err_message": "NoUserFound"
   *     }
   */
  public function fetchProfile($user_id) {
    $user = User::getUserByUserId($user_id);
    if ($user) {
      return json_encode(array(
        'user_id' => $user_id,
        'email' => $user->email,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'phone' => $user->phone,
        'local_address' => $user->local_address,
      ));
    }
    else {
      return json_encode(array(
        'err_message' => 'NoUserFound',
      ));
    }
  }

}