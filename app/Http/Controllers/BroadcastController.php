<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\AdImages;
use App\Models\Service;

class BroadcastController extends Controller
{

  /**
   * @api {get} broadcast/{raw_code} Fetch All The Broadcast Data of the Business
   * @apiName FetchBroadcastDetails
   * @apiGroup Broadcast
   * @apiVersion 1.0.0
   * @apiExample {js} Example Usage:
   *     https://api.featherq.com/broadcast/pg21
   *     https://api.featherq.com/broadcast/reminisense-corp
   * @apiDescription Gets all the needed data to make the broadcast page functional.
   *
   * @apiHeader {String} access-key The unique access key sent by the client.
   * @apiPermission Authenticated & Anonymous Users
   *
   * @apiParam {String} raw_code The 4 digit code or the personalized url of the business.
   *
   * @apiSuccess (Success 200) {String} business_id The id of the image.
   * @apiSuccess (Success 200) {String[]} ad_images An array containing the image advertisements of the business.
   * @apiSuccess (Success 200) {String} open_hour The hour that the business opens.
   * @apiSuccess (Success 200) {String} open_minute The minute that the business opens.
   * @apiSuccess (Success 200) {String} open_ampm The ampm that the business opens.
   * @apiSuccess (Success 200) {String} close_hour The hour that the business closes.
   * @apiSuccess (Success 200) {String} close_minute The minute that the business closes.
   * @apiSuccess (Success 200) {String} close_ampm The ampm that the business closes.
   * @apiSuccess (Success 200) {String} local_address The address of the business.
   * @apiSuccess (Success 200) {String} business_name The name of the business.
   * @apiSuccess (Success 200) {String} first_service The default service of the business.
   * @apiSuccess (Success 200) {String} keywords Some keywords used for broadcast meta data.
   * @apiSuccessExample {Json} Success-Response:
   *     HTTP/1.1 200 OK
   *     {
   *       "business_id": 125,
   *       "ad_images": [
   *         {
   *         "img_id": 72,
   *         "path": "ads\/125\/o_1a2pute0r17ns1fi91p8q1vj6ric.jpg",
   *         "weight": 19,
   *         "business_id": 125
   *         },
   *         {
   *         "img_id": 73,
   *         "path": "ads\/125\/o_1a2pute0r1u9b83k1rj45ii12pvd.jpg",
   *         "weight": 20,
   *         "business_id": 125
   *         },
   *         {
   *         "img_id": 74,
   *         "path": "ads\/125\/o_1a2pute0rmt3nm7f5o10927tue.png",
   *         "weight": 21,
   *         "business_id": 125
   *         }
   *       ],
   *       "open_hour": 3,
   *       "open_minute": 0,
   *       "open_ampm": "AM",
   *       "close_hour": 4,
   *       "close_minute": 0,
   *       "close_ampm": "PM",
   *       "local_address": "Fuente Osme\u00f1a Circle, Cebu City, Central Visayas, Philippines",
   *       "business_name": "Paul's Putohan",
   *       "first_service": {
   *         "service_id": 125,
   *         "code": "",
   *         "name": "Paul's Putohan Service",
   *         "status": 1,
   *         "time_created": "2015-07-22 07:56:43",
   *         "branch_id": 125,
   *         "repeat_type": "daily"
   *       },
   *       "keywords": [
   *         "food",
   *         "beverage"
   *       ]
   *     }
   *
   * @apiError (Error 404) {String} NoBusinessFound The <code>NoBusinessFound</code> is null.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 404 Not Found
   *     {
   *       "err_message": "NoBusinessFound"
   *     }
   */
  public function fetchDetails($raw_code = '') {
    $business_id = Business::getBusinessIdByRawCode($raw_code);
    if ($business_id) {
      //$data = json_decode(file_get_contents(public_path() . '/json/' . $business_id . '.json'));
      $business_name = Business::name($business_id);
      $open_hour = Business::openHour($business_id);
      $open_minute = Business::openMinute($business_id);
      $open_ampm = Business::openAMPM($business_id);
      $close_hour = Business::closeHour($business_id);
      $close_minute = Business::closeMinute($business_id);
      $close_ampm = Business::closeAMPM($business_id);
      $ad_images = AdImages::fetchAllImagesByBusinessId($business_id);
      $first_service = Service::getFirstServiceOfBusiness($business_id);
      //$allow_remote = QueueSettings::allowRemote($first_service->service_id);
      $local_address = Business::localAddress($business_id);
      //$lines_in_queue = Analytics::getBusinessRemainingCount($business_id);
      //$estimate_serving_time = Analytics::getAverageTimeServedByBusinessId($business_id, 'string', $date, $date);
      $keywords = Business::getKeywordsByBusinessId($business_id);
      return json_encode(array(
        'business_id' => $business_id,
        //'adspace_size' => $data->adspace_size,
        //'carousel_delay' => $data->carousel_delay ? (int)$data->carousel_delay : 5000,
        //'ad_type' => $data->ad_type,
        'ad_images' => $ad_images,
        //'box_num' => explode("-", $data->display)[1], // the second index tells how many numbers to show in the broadcast screen
        'open_hour' => $open_hour,
        'open_minute' => $open_minute,
        'open_ampm' => $open_ampm,
        'close_hour' => $close_hour,
        'close_minute' => $close_minute,
        'close_ampm' => $close_ampm,
        'local_address' => $local_address,
        'business_name' => $business_name,
        //'lines_in_queue' => $lines_in_queue,
        //'estimate_serving_time' => $estimate_serving_time,
        'first_service' => $first_service,
        //'allow_remote' => $allow_remote,
        'keywords' => $keywords,
      ));
    }
    else {
      return json_encode(array(
        'err_message' => 'NoBusinessFound',
      ));
    }
  }

}