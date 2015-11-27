<?php

namespace App\Http\Controllers;

use App\Models\BroadcastSettings;
use App\Models\Business;
use App\Models\AdImages;
use App\Models\Service;

class BroadcastController extends Controller
{

  /**
   * @api {get} broadcast/{raw_code} Fetch all the Business Broadcast Data
   * @apiName FetchBroadcastDetails
   * @apiGroup Broadcast
   * @apiVersion 1.0.0
   * @apiExample {js} Example Usage:
   *     https://api.featherq.com/broadcast/pg21
   *     https://api.featherq.com/broadcast/reminisense-corp
   * @apiDescription Gets all the needed data to make the broadcast page functional.
   *
   * @apiHeader {String} access-key The unique access key sent by the client.
   * @apiPermission none
   *
   * @apiParam {String} raw_code The 4 digit code or the personalized url of the business.
   *
   * @apiSuccess (200) {String} business_id The id of the image.
   * @apiSuccess (200) {String} adspace_size The space size of the advertisement image.
   * @apiSuccess (200) {String} numspace_size The space size of the broadcast numbers.
   * @apiSuccess (200) {Number} box_num The number of broadcast numbers to show on the screen.
   * @apiSuccess (200) {Number} get_num The available number for remote queuing.
   * @apiSuccess (200) {String} display The display type code.
   * @apiSuccess (200) {Boolean} show_issued The flag to show only called numbers or also the issued numbers.
   * @apiSuccess (200) {String} ad_video The video ad url.
   * @apiSuccess (200) {Boolean} turn_on_tv The flag to check if the tv is on.
   * @apiSuccess (200) {String} tv_channel The current channel of the tv.
   * @apiSuccess (200) {String} date The current date of business operations.
   * @apiSuccess (200) {String} ticker_message The first line of ticker message.
   * @apiSuccess (200) {String} ticker_message2 The second line of ticker message.
   * @apiSuccess (200) {String} ticker_message3 The third line of ticker message.
   * @apiSuccess (200) {String} ticker_message4 The fourth line of ticker message.
   * @apiSuccess (200) {String} ticker_message5 The fifth line of ticker message.
   * @apiSuccess (200) {String[]} ad_images An array containing the image advertisements of the business.
   * @apiSuccess (200) {String} open_hour The hour that the business opens.
   * @apiSuccess (200) {String} open_minute The minute that the business opens.
   * @apiSuccess (200) {String} open_ampm The ampm that the business opens.
   * @apiSuccess (200) {String} close_hour The hour that the business closes.
   * @apiSuccess (200) {String} close_minute The minute that the business closes.
   * @apiSuccess (200) {String} close_ampm The ampm that the business closes.
   * @apiSuccess (200) {String} local_address The address of the business.
   * @apiSuccess (200) {String} business_name The name of the business.
   * @apiSuccess (200) {String} first_service The default service of the business.
   * @apiSuccess (200) {String[]} keywords Some keywords used for broadcast meta data.
   * @apiSuccessExample {Json} Success-Response:
   *     HTTP/1.1 200 OK
   *     {
   *       "business_id": 125,
   *        "adspace_size": "117px",
   *         "numspace_size": "117px",
   *         "carousel_delay": 5000,
   *         "ad_type": "carousel",
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
   *      "box_num": "10",
   *       "get_num": 32,
   *       "display": "1-10",
   *       "show_issued": "true",
   *       "ad_video": "\\\/\\\/www.youtube.com\\\/embed\\\/EMnDdH8fdEc",
   *       "turn_on_tv": "false",
   *       "tv_channel": "",
   *       "date": "111315",
   *       "ticker_message": "Read",
   *       "ticker_message2": "Yes",
   *       "ticker_message3": "Toast",
   *       "ticker_message4": "",
   *       "ticker_message5": "Yum",
   *       "open_hour": 3,
   *       "open_minute": 0,
   *       "open_ampm": "AM",
   *       "close_hour": 4,
   *       "close_minute": 0,
   *       "close_ampm": "PM",
   *       "local_address": "Disneyland, Hongkong",
   *       "business_name": "Foo Example",
   *       "first_service": {
   *         "service_id": 125,
   *         "code": "",
   *         "name": "Foo Example Service",
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
   * @apiError (200) {String} NoBusinessFound No Businesses were found using the <code>raw_code</code>.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 200 OK
   *     {
   *       "err_code": "NoBusinessFound"
   *     }
   */
  public function getDetails($raw_code = '') {
    if (Business::businessExistsByRawCode($raw_code)) {
      $business_id = Business::getBusinessIdByRawCode($raw_code);
      //$data = json_decode(file_get_contents(public_path() . '/json/' . $business_id . '.json'));
      $data = BroadcastSettings::fetchAllSettingsByBusiness($business_id);
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
        'adspace_size' => $data->adspace_size,
        'numspace_size' => $data->numspace_size,
        'carousel_delay' => $data->carousel_delay,
        'ad_type' => $data->ad_type,
        'ad_images' => $ad_images,
        'box_num' => explode("-", $data->display)[1], // the second index tells how many numbers to show in the broadcast screen
        'get_num' => $data->get_num,
        'display' => $data->display,
        'show_issued' => $data->show_issued,
        'ad_video' => $data->ad_video,
        'turn_on_tv' => $data->turn_on_tv,
        'tv_channel' => $data->tv_channel,
        'date' => $data->date,
        'ticker_message' => $data->ticker_message,
        'ticker_message2' => $data->ticker_message2,
        'ticker_message3' => $data->ticker_message3,
        'ticker_message4' => $data->ticker_message4,
        'ticker_message5' => $data->ticker_message5,
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
                  'err_code' => 'NoBusinessFound',
      ));
    }
  }

}