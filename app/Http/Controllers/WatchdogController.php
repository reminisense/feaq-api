<?php
/**
 * Created by IntelliJ IDEA.
 * User: polljii
 * Date: 4/28/15
 * Time: 2:26 PM
 */

namespace App\Http\Controllers;

use App\Models\Watchdog;
use App\Models\User;

class WatchdogController extends Controller
{

//  public function postLogSearch() {
//    $post = json_decode(file_get_contents("php://input"));
//    if ($post) {
//      Watchdog::createRecord(array(
//        'user_id' => Helper::userId(),
//        'action_type' => 'search',
//        'value' => serialize(array(
//          'keyword' => $post->keyword,
//          'country' => $post->country,
//          'industry' => $post->industry,
//          'time_open' => $post->time_open,
//          'ip_address' => Helper::getIP(),
//          'latitude' => $post->latitude,
//          'longitude' => $post->longitude,
//          'timestamp' => time(),
//        )),
//      ));
//    }
//  }

//  public function postLogVisit(){
//    $input = Input::all();
//    $user_id = Helper::userId();
//
//    //get user environment information
//    $data = [
//      'ip_address'        => Helper::getIP(),
//      'referrer_url'      => $input['referrer_url'],
//      'page_url'          => $input['page_url'],
//      'latitude'          => $input['latitude'],
//      'longitude'         => $input['longitude'],
//      'browser'           => $input['browser'],
//      'operating_system'  => $input['operating_system'],
//      'screen_size'       => $input['screen_size'],
//    ];
//
//    //get user data
//    if($user_id){
//      $birthdate              = User::birthdate($user_id);
//      $data['gender']         = User::gender($user_id);
//      $data['nationality']    = User::nationality($user_id);
//      $data['civil_status']   = User::civil_status($user_id);
//      $data['birth_day']      = $birthdate ? date('d', $birthdate) : null;
//      $data['birth_month']    = $birthdate ? date('m', $birthdate) : null;
//      $data['birth_year']     = $birthdate ? date('Y', $birthdate) : null;
//      $data['age']            = User::age($user_id);
//    }
//
//        //get page information
//        $url_data = explode('/', $input['page_url']);
//        if($url_data[3] == 'broadcast' && $url_data[4] == 'business'){
//            $business_id            = $url_data[5];
//            $data['business_id']    = $business_id;
//
//            try{
//                $data['industry']       = Business::industry($business_id);
//                $data['local_address']  = Business::localAddress($business_id);
//            }catch(Exception $e){}
//
//        }
//
//    $log_data = [
//      'user_id'           => Helper::userId(),
//      'action_type'       => 'page_view',
//      'value'             => serialize($data),
//    ];
//
//    $id = Watchdog::createRecord($log_data);
//    return json_encode(['success' => 1, 'log_id' => $id]);
//  }

    /**
     * @api {post} /admin/watchdog/{user_id}/{keyword} Retrieve watchdog data.
     * @apiName RetrieveWatchdogData
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/watchdog/1/browser
     * @apiDescription Retrieve watchdog information
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {Number} user_id User ID to search data.
     * @apiParam {String} keyword Keyword to search.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     * @apiSuccess (200) {Object} data Data matching the search <code>keyword</code>.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *      "success": 1,
     *      "data": {
     *              "Chrome 42.0.2311.135": 12,
     *              "Chrome 42.0.2311.152": 14,
     *              "Safari 8.0": 23,
     *              "Safari 7.0": 17,
     *              "Firefox 37.0": 1
     *          }
     *      }
     *
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code UserNotFound No user matched using <code>user_id</code>
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
     *     }
     */
    public function getUserdata($user_id = null, $keyword = null)
    {
        // TODO authentication
        if (true) {

            $user = User::getUserByUserId($user_id);
            if(is_null($user)) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'UserNotFound'
                ));
            }

            // TODO since API auth is not yet done, we set user_id to 0 for now.
            $data = Watchdog::queryUserInfo($keyword, $user_id);
            return json_encode(array(
                'success' => 1,
                'data' => $data
            ));
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }
}