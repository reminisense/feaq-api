<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 19/11/15
 * Time: 3:57 PM
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Input;

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
   * @apiSuccess (200) {Number} user_id The id of the user.
   * @apiSuccess (200) {String} email The email address of the user.
   * @apiSuccess (200) {String} first_name The first name of the user.
   * @apiSuccess (200) {String} last_name The last name of the user.
   * @apiSuccess (200) {String} phone The phone number of the user.
   * @apiSuccess (200) {String} local_address The address of the user.
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
   * @apiError (200) {String} UserNotFound There were no users found with the given <code>user_id</code>.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 200 OK
   *     {
   *       "err_code": "UserNotFound"
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
        'err_code' => 'NoUserFound',
      ));
    }
  }

    /**
     * @api {put} user/update Update User Information
     * @apiName UpdateUserInfo
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/user/update
     * @apiDescription Update user information using the information given from JSON.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Current User & Admin
     *
     * @apiParam {Number} user_id The id of the user.
     * @apiParam {String} first_name The modified first name of user.
     * @apiParam {String} last_name The modified last name of user.
     * @apiParam {String} [phone] The modified contact number of user.
     * @apiParam {String} [local_address] The modified address of user.
     *
     * @apiSuccess (200) {String} success The flag indicating the success/failure of update process. Returns <code>1</code> if process was successful.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "success" : 1
     *      }
     *
     * @apiError (200) {Object} success The flag indicating the success/failure of update process. Returns <code>0</code> if process was not successful.
     * @apiError (200) {Object} UserNotFound There were no users found with the given <code>user_id</code>.
     * @apiError (200) {Object) SomethingWentWrong Something went wrong while saving your data.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "success": "0",
     *          "err_code": "UserNotFound"
     *     }
     */
    public function updateUser()
    {
        $userData = Input::all();
        $user = User::find(isset($userData['user_id']) ? $userData['user_id'] : '');
        if (is_null($user)) {
            return response()->json([
                'success' => 0,
                'err_code' => 'UserNotFound'
            ]);
        }

        $user->first_name = isset($userData['first_name']) ? $userData['first_name'] : $user->first_name;
        $user->last_name = isset($userData['last_name']) ? $userData['last_name'] : $user->last_name;
        $user->phone = isset($userData['phone']) ? $userData['phone'] : $user->phone;
        $user->local_address = isset($userData['local_address']) ? $userData['local_address'] : $user->local_address;

        if ($user->save()) {
            return response()->json([
                'success' => 1
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'err_code' => 'SomethingWentWrong'
            ]);
        }
    }
}