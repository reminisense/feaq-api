<?php

namespace App\Http\Controllers;

use App\Models\BroadcastSettings;
use App\Models\Business;
use App\Models\AdImages;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class BroadcastController extends Controller
{

    /**
     * @api {get} broadcast/{raw_code} Fetch Business Broadcast Data
     * @apiName FetchBroadcastDetails
     * @apiGroup Broadcast
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/broadcast/pg21
     *     https://api.featherq.com/broadcast/reminisense-corp
     * @apiDescription Gets all the data needed to make the broadcast page functional.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {String} raw_code The 4 digit code or the personalized url of the business.
     *
     * @apiSuccess (200) {Number} business_id The id of the business which owns the broadcast screen.
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
     * @apiError (Error) {String} BusinessNotFound No businesses were found using the <code>raw_code</code>.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "BusinessNotFound"
     *     }
     */
    public function getDetails($raw_code = '')
    {
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
        } else {
            return json_encode(array(
                'err_code' => 'BusinessNotFound',
            ));
        }
    }

    /**
     * @api {put} broadcast/update/{business_id} Update Broadcast.
     * @apiName UpdateBroadcast
     * @apiGroup Broadcast
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/broadcast/update/1
     * @apiDescription Updates broadcast information for business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} business_id Unique ID of business.
     * @apiParam {Number} adspace_size Size of adspace.
     * @apiParam {Number} numspace_size Size of num.
     * @apiParam {String} ad_type Type of advertisement.
     * @apiParam {Number} carousel_delay Delay of carousel in seconds.
     * @apiParam {String} tv_channel TV Channel.
     * @apiParam {Number} show_issued Show issued flag.
     * @apiParam {String} ticker_message1 Ticker message 1.
     * @apiParam {String} ticker_message2 Ticker message 2.
     * @apiParam {String} ticker_message3 Ticker message 3.
     * @apiParam {String} ticker_message4 Ticker message 4.
     * @apiParam {String} ticker_message5 Ticker message 5.
     * @apiParam {Number} num_boxes Number of boxes.
     *
     * @apiSuccess (200) {Number} success Success flag.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *       "success": 1
     *      }
     *
     * @apiError (Error) {Number} success Fail flag.
     * @apiError (Error) {Number} err_code BusinessNotFound No businesses were found using the <code>business_id</code>.
     * @apiError (Error) {String} err_code SomethingWentWrong Something went wrong when updating settings. Please try again.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success" : 0,
     *          "err_code": "BusinessNotFound"
     *      }
     */
    public function saveSettings($business_id = null)
    {
        $business = Business::getBusinessByBusinessId($business_id);
        if (!is_null($business)) {
            try {
                $path = base_path('public/json/' . $business_id . '.json');
                $json = file_get_contents($path);
                $data = json_decode($json);
                $data->adspace_size = Input::get('adspace_size');
                $data->numspace_size = Input::get('numspace_size');
                $data->ad_type = Input::get('ad_type');
                $data->carousel_delay = Input::get('carousel_delay') * 1000; // convert from second to millisecond
                if ($data->ad_type == 'internet_tv') {
                    $data->tv_channel = Input::get('tv_channel');
                }
                $data->display = $this->generateDisplayCode($data->ad_type, Input::get('num_boxes'));
                $data->show_issued = Input::get('show_issued');
                $data->ticker_message = Input::get('ticker_message');
                $data->ticker_message2 = Input::get('ticker_message2');
                $data->ticker_message3 = Input::get('ticker_message3');
                $data->ticker_message4 = Input::get('ticker_message4');
                $data->ticker_message5 = Input::get('ticker_message5');
                $data = $this->boxObjectCreator($data, Input::get('num_boxes'));
                $encode = json_encode($data);
                file_put_contents($path, $encode);
                return json_encode(array('success' => 1));
            } catch (Exception $e) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'SomethingWentWrong'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'BusinessNotFound'
            ));
        }

    }

    // generate a representation for the combination of ad_type and num_boxes
    private function generateDisplayCode($ad_type, $num_boxes)
    {
        if ($ad_type == 'carousel') {
            $display = '1-';
        } elseif ($ad_type == 'internet_tv') {
            $display = '2-';
        } else {
            $display = '0-';
        }
        return $display . $num_boxes;
    }

    private function boxObjectCreator($data, $num_boxes)
    {
        if ($num_boxes >= '2') {
            $data->box2 = new stdClass();
            $data->box2->number = '';
            $data->box2->terminal = '';
            $data->box2->rank = '';
        }
        if ($num_boxes >= '3') {
            $data->box3 = new stdClass();
            $data->box3->number = '';
            $data->box3->terminal = '';
            $data->box3->rank = '';
        }
        if ($num_boxes >= '4') {
            $data->box4 = new stdClass();
            $data->box4->number = '';
            $data->box4->terminal = '';
            $data->box4->rank = '';
        }
        if ($num_boxes >= '5') {
            $data->box5 = new stdClass();
            $data->box5->number = '';
            $data->box5->terminal = '';
            $data->box5->rank = '';
        }
        if ($num_boxes >= '6') {
            $data->box6 = new stdClass();
            $data->box6->number = '';
            $data->box6->terminal = '';
            $data->box6->rank = '';
        }
        if ($num_boxes >= '7') {
            $data->box7 = new stdClass();
            $data->box7->number = '';
            $data->box7->terminal = '';
            $data->box7->rank = '';
        }
        if ($num_boxes >= '8') {
            $data->box8 = new stdClass();
            $data->box8->number = '';
            $data->box8->terminal = '';
            $data->box8->rank = '';
        }
        if ($num_boxes >= '9') {
            $data->box9 = new stdClass();
            $data->box9->number = '';
            $data->box9->terminal = '';
            $data->box9->rank = '';
        }
        if ($num_boxes == '10') {
            $data->box10 = new stdClass();
            $data->box10->number = '';
            $data->box10->terminal = '';
            $data->box10->rank = '';
        }
        $data = $this->boxObjectUnsetter($data, $num_boxes);
        return $data;
    }

    private function boxObjectUnsetter($data, $num_boxes)
    {
        if ($num_boxes == '1') {
            unset($data->box2);
            unset($data->box3);
            unset($data->box4);
            unset($data->box5);
            unset($data->box6);
            unset($data->box7);
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '2') {
            unset($data->box3);
            unset($data->box4);
            unset($data->box5);
            unset($data->box6);
            unset($data->box7);
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '3') {
            unset($data->box4);
            unset($data->box5);
            unset($data->box6);
            unset($data->box7);
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '4') {
            unset($data->box5);
            unset($data->box6);
            unset($data->box7);
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '5') {
            unset($data->box6);
            unset($data->box7);
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '6') {
            unset($data->box7);
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '7') {
            unset($data->box8);
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '8') {
            unset($data->box9);
            unset($data->box10);
        } elseif ($num_boxes == '9') {
            unset($data->box10);
        }
        return $data;
    }
}