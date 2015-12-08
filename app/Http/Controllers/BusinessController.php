<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/17/2015
 * Time: 10:54 AM
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Models\Business;
use App\Models\Terminal;
use App\Models\Service;
use App\Models\Analytics;
use App\Models\QueueSettings;
use App\Models\Helper;
use App\Models\Branch;
use App\Models\TerminalUser;
use App\Models\UserBusiness;

class BusinessController extends Controller
{
    /**
     * @api {get} /business/search Search Businesses
     * @apiName Search
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/business/search?keyword=&country=&industry=&time_open=&timezone=&limit=&offset
     * @apiDescription Search for businesses based on the given parameters.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {String}  keyword The keyword or name used to search for the business.
     * @apiParam {String} [country] The country of the business.
     * @apiParam {String} [industry] The industry of the business.
     * @apiParam {String} [time_open] The time the business opens. (e.g. <code>11:00 AM</code>)
     * @apiParam {String} [timezone] The timezone of the business. (e.g. <code>Asia/Singapore</code>)
     * @apiParam {Number} [limit] The maximum number of entries to be retrieved.
     * @apiParam {Number} [offset] The number where the entries retrieved will start.
     *
     * @apiSuccess (200) {Object[]} business Array of objects with business details.
     * @apiSuccess (200) {Number} business.business_id The business id of the retrieved business from the database.
     * @apiSuccess (200) {String} business.business_name The name of the business.
     * @apiSuccess (200) {String} business.local_address The address of the business.
     * @apiSuccess (200) {String} business.time_open The time that the business opens.
     * @apiSuccess (200) {String} business.time_close The time that the business closes.
     * @apiSuccess (200) {String} business.waiting_time Indicates how heavy the queue is based on time it takes for the last number in the queue to be called.
     * @apiSuccess (200) {Number} business.last_number_called The last number called by the business.
     * @apiSuccess (200) {Number} business.next_available_number The next number that can be placed to the queue.
     * @apiSuccess (200) {Number} business.last_active The number of days when the business last processed the queue.
     * @apiSuccess (200) {Boolean} business.card_bool Indicates if the business is active or not.
     *
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          [
     *              "business_id": 1,
     *              "business_name": "Angel's Burger",
     *              "local_address": "Hernan Cortes st. Subangdako, Mandaue City",
     *              "time_open": "10:00 AM",
     *              "time_close": "4:00 PM",
     *              "waiting_time": "light",
     *              "last_number_called": "none",
     *              "next_available_number": 1,
     *              "last_active": 5,
     *              "card_bool": false
     *          ]
     *      }
     *
     */
    public function search(){
        $business = Business::searchBusiness($_GET);
        return json_encode($business);
    }

    /**
     * @api {get} /business/search-suggest/{keyword} Search Suggestions
     * @apiName SearchSuggest
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/business/search-suggest/keyword
     * @apiDescription Suggests search items for businesses based on the given keyword.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {String} keyword The keyword used to search for the business.
     *
     * @apiSuccess (200) {Object[]} business Array of objects with business details.
     * @apiSuccess (200) {String} business.business_name The name of the business.
     * @apiSuccess (200) {String} business.local_address The address of the business.
     *
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          [
     *              "business_name": "Angel's Burger",
     *              "local_address": "Hernan Cortes st. Subangdako, Mandaue City",
     *          ]
     *      }
     *
     */
    public function searchSuggest($keyword){
        $businesses = Business::searchSuggest($keyword);
        return json_encode($businesses);
    }

    /**
     * @api {get} business/{business_id} Fetch Business Details
     * @apiName FetchBusinessDetails
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/business/1
     * @apiDescription Gets all the information related to the business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Business Owner
     *
     * @apiParam {Number} business_id The id of the business.
     *
     * @apiSuccess (200) {Object} business The business object.
     * @apiSuccess (200) {Number} business.business_id The id of the business.
     * @apiSuccess (200) {String} business.name The name of the business.
     * @apiSuccess (200) {String} business.raw_code The unique 4-digit code of the business.
     * @apiSuccess (200) {String} business.local_address The location of the business.
     * @apiSuccess (200) {String} business.fb_url The facebook page of the business.
     * @apiSuccess (200) {String} business.industry The type of industry the business caters.
     * @apiSuccess (200) {Number} business.open_hour The hour the business opens.
     * @apiSuccess (200) {Number} business.open_minute The minute the business opens.
     * @apiSuccess (200) {Number} business.open_ampm The ampm the business opens.
     * @apiSuccess (200) {Number} business.close_hour The hour the business closes.
     * @apiSuccess (200) {Number} business.close_minute The minute the business closes.
     * @apiSuccess (200) {Number} business.close_ampm The ampm the business closes.
     * @apiSuccess (200) {String} business.timezone The timezone the business is located.
     * @apiSuccess (200) {Number} business.queue_limit The maximum number of priority numbers to give.
     * @apiSuccess (200) {Object} queue_settings The different settings of the queuing process of the business.
     * @apiSuccess (200) {Boolean} queue_settings.terminal_specific_issue A flag to identify if terminals can only call numbers they issued.
     * @apiSuccess (200) {String} queue_settings.sms_current_number The number linked to the business for SMS capabilities.
     * @apiSuccess (200) {String} queue_settings.sms_1_ahead .
     * @apiSuccess (200) {String} queue_settings.sms_5_ahead .
     * @apiSuccess (200) {String} queue_settings.sms_10_ahead .
     * @apiSuccess (200) {String} queue_settings.sms_blank_ahead .
     * @apiSuccess (200) {String} queue_settings.input_sms_field .
     * @apiSuccess (200) {Boolean} queue_settings.allow_remote A flag to identify if the business allows remote queuing.
     * @apiSuccess (200) {Number} queue_settings.remote_limit The maximum number of priority numbers allowed for remote queuing.
     * @apiSuccess (200) {Object[]} terminals The details and information of the business terminals.
     * @apiSuccess (200) {Number} terminals.terminal_id The id of the terminal.
     * @apiSuccess (200) {String} terminals.code The code of the terminal.
     * @apiSuccess (200) {Number} terminals.service_id The service id the terminal belongs.
     * @apiSuccess (200) {Number} terminals.name The name of the terminal.
     * @apiSuccess (200) {String} terminals.time_created The time the terminal was created.
     * @apiSuccess (200) {Number} terminals.box_rank .
     * @apiSuccess (200) {Object} terminals.users The list of users assigned to a terminal.
     * @apiSuccess (200) {Number} terminals.users.terminal_user_id The id of the user assigned to the terminal.
     * @apiSuccess (200) {Boolean} terminals.users.status A flag to determine if the terminal is closed or not.
     * @apiSuccess (200) {Number} terminals.users.date Timestamp that the user was assigned to the terminal.
     * @apiSuccess (200) {String} terminals.users.first_name The first name of the terminal user.
     * @apiSuccess (200) {String} terminals.users.last_name The last name of the terminal user.
     * @apiSuccess (200) {Object} analytics Analytics data allowed for viewing by the business.
     * @apiSuccess (200) {Number} analytics.remaining_count .
     * @apiSuccess (200) {Number} analytics.total_numbers_issued The total numbers issued by the business.
     * @apiSuccess (200) {Number} analytics.total_numbers_called The total numbers called by the business.
     * @apiSuccess (200) {Number} analytics.total_numbers_served The total numbers served by the business.
     * @apiSuccess (200) {Number} analytics.total_numbers_dropped The total numbers dropped by the business.
     * @apiSuccess (200) {Number} analytics.average_time_called The average time called for each priority number.
     * @apiSuccess (200) {Number} analytics.average_time_served The average time served for each priority number.
     * @apiSuccess (200) {Object} sms_settings The sms settings of the business.
     * @apiSuccess (200) {String} sms_settings.sms_gateway The sms gateway of the business.
     * @apiSuccess (200) {String} sms_settings.twilio_account_sid The account id of the business to Twilio.
     * @apiSuccess (200) {String} sms_settings.twilio_auth_token The unique token of the business to use Twilio.
     * @apiSuccess (200) {String} sms_settings.twilio_phone_number The phone number linked to Twilio.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "business": {
     *           "business_id": 168,
     *           "name": "Reminisense Coporation",
     *           "raw_code": "izzv",
     *           "industry": "Software Development",
     *           "open_hour": 10,
     *           "open_minute": 0,
     *           "open_ampm": "AM",
     *           "close_hour": 10,
     *           "close_minute": 0,
     *           "close_ampm": "PM",
     *           "timezone": "Asia\/Manila",
     *           "local_address": "Hernan Cortes Street, Mandaue City, Central Visayas, Philippines",
     *           "num_terminals": 1,
     *           "queue_limit": 9999,
     *           "fb_url": "",
     *           "business_features": null
     *       },
     *       "terminals": [
     *           {
     *               "terminal_id": 448,
     *               "name": "Terminal 1",
     *               "code": "",
     *               "service_id": 168,
     *               "status": 1,
     *               "time_created": "2015-11-25 02:48:37",
     *               "box_rank": 1,
     *               "users": [
     *                   {
     *                       "terminal_user_id": 496,
     *                       "user_id": 13,
     *                       "terminal_id": 448,
     *                       "status": 1,
     *                       "date": 1448380800,
     *                       "first_name": "Paul Andrew \"Wizard of Love\"",
     *                       "last_name": "Gutib"
     *                   }
     *               ]
     *           }
     *       ],
     *       "analytics": {
     *           "remaining_count": 0,
     *           "total_numbers_issued": 0,
     *           "total_numbers_called": 0,
     *           "total_numbers_served": 0,
     *           "total_numbers_dropped": 0,
     *           "average_time_called": "",
     *           "average_time_served": ""
     *       },
     *       "queue_settings": {
     *           "terminal_specific_issue": 0,
     *           "sms_current_number": 0,
     *           "sms_1_ahead": 0,
     *           "sms_5_ahead": 0,
     *           "sms_10_ahead": 0,
     *           "sms_blank_ahead": 0,
     *           "input_sms_field": 0,
     *           "allow_remote": 0,
     *           "remote_limit": 0
     *       },
     *       "sms_settings": {
     *           "sms_gateway": null,
     *           "twilio_account_sid": null,
     *           "twilio_auth_token": null,
     *           "twilio_phone_number": null
     *       }
     *     }
     *
     * @apiError (Error) {String} NoBusinessFound No businesses were found using the param <code>business_id</code>.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "NoBusinessFound"
     *     }
     */
    public function getDetails($business_id = 0) {
        if (Business::businessExistsByBusinessId($business_id)) {
            $business = Business::fetchBusinessDetails($business_id);
            unset($business->country_code);
            unset($business->area_code);
            unset($business->registration_date);
            unset($business->last_active_date);
            unset($business->status);
            unset($business->override);
            unset($business->zip_code);
            unset($business->longitude);
            unset($business->latitude);
            $terminals = Terminal::getTerminalsByBusinessId($business_id);
            $terminals = Terminal::getAssignedTerminalWithUsers($terminals);
            $analytics = Analytics::getBusinessAnalytics($business_id);
            $first_service = Service::getFirstServiceOfBusiness($business_id);
            return json_encode(array(
              'business' => $business,
              'terminals' => $terminals,
              'analytics' => $analytics,
              'queue_settings' => $this->queueSettings($first_service),
              'sms_settings' => $this->smsSettings($first_service, $business),
            ));
        }
        else {
            return json_encode(array(
              'err_code' => 'NoBusinessFound'
            ));
        }
    }

    private function queueSettings($first_service) {
        return array(
            'terminal_specific_issue' => QueueSettings::terminalSpecificIssue($first_service->service_id),
            'sms_current_number' => QueueSettings::smsCurrentNumber($first_service->service_id),
            'sms_1_ahead' => QueueSettings::smsOneAhead($first_service->service_id),
            'sms_5_ahead' => QueueSettings::smsFiveAhead($first_service->service_id),
            'sms_10_ahead' => QueueSettings::smsTenAhead($first_service->service_id),
            'sms_blank_ahead' => QueueSettings::smsBlankAhead($first_service->service_id),
            'input_sms_field' => QueueSettings::inputSmsField($first_service->service_id),
            'allow_remote' => QueueSettings::allowRemote($first_service->service_id),
            'remote_limit' => QueueSettings::remoteLimit($first_service->service_id),
        );
    }

    private function smsSettings($first_service, $business) {
        $business_details = array();
        $sms_gateway_api = unserialize(QueueSettings::smsGatewayApi($first_service->service_id));
        if($business['sms_gateway'] == 'frontline_sms' && $sms_gateway_api){
            $business_details['frontline_sms_url'] = $sms_gateway_api['frontline_sms_url'];
            $business_details['frontline_sms_api_key'] = $sms_gateway_api['frontline_sms_api_key'];
        }elseif($business['sms_gateway'] == 'twilio' && $sms_gateway_api){
            if($sms_gateway_api['twilio_account_sid'] == TWILIO_ACCOUNT_SID &&
              $sms_gateway_api['twilio_auth_token'] == TWILIO_AUTH_TOKEN &&
              $sms_gateway_api['twilio_phone_number'] == TWILIO_PHONE_NUMBER){
                $business_details['sms_gateway'] = NULL;
                $business_details['twilio_account_sid'] = NULL;
                $business_details['twilio_auth_token'] = NULL;
                $business_details['twilio_phone_number'] = NULL;
            }else{
                $business_details['twilio_account_sid'] = $sms_gateway_api['twilio_account_sid'];
                $business_details['twilio_auth_token'] = $sms_gateway_api['twilio_auth_token'];
                $business_details['twilio_phone_number'] = $sms_gateway_api['twilio_phone_number'];
            }
        }else{
            $business_details['sms_gateway'] = NULL;
            $business_details['twilio_account_sid'] = NULL;
            $business_details['twilio_auth_token'] = NULL;
            $business_details['twilio_phone_number'] = NULL;
        }
        return $business_details;
    }

    /**
     * @api {post} /business/delete Delete Business
     * @apiName DeleteBusiness
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/business/delete
     * @apiDescription Deletes the business along with its branches, services, and terminals.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Admin & Business Owner
     *
     * @apiParam {Number} business_id The id of the business to delete.
     *
     * @apiSuccess (200) {Boolean} status A true boolean.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "status": 1
     *      }
     *
     * @apiError (Error) {String} NoBusinessFound No businesses were found using the param <code>business_id</code>.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "NoBusinessFound"
     *     }
     */
    public function deleteRecord() {
        $business_id = Input::get('business_id');
        if (Business::businessExistsByBusinessId($business_id)) {
            Business::deleteBusinessByBusinessId($business_id);
            $branches = Branch::getBranchesByBusinessId($business_id);
            foreach ($branches as $count => $data) {
                $services = Service::getServicesByBranchId($data->branch_id);
                foreach ($services as $count2 => $data2) {
                    $terminals = Terminal::getTerminalsByServiceId($data2->service_id);
                    foreach ($terminals as $count3 => $data3) {
                        TerminalUser::deleteUserByTerminalId($data3['terminal_id']);
                    }
                    Terminal::deleteTerminalsByServiceId($data2->service_id);
                }
                Service::deleteServicesByBranchId($data->branch_id);
            }
            Branch::deleteBranchesByBusinessId($business_id);
            UserBusiness::deleteUserByBusinessId($business_id);
            return json_encode(array('status' => 1));
        }
        else {
            return json_encode(array(
              'err_code' => 'NoBusinessFound'
            ));
        }
    }


    /**
     * @api {put} /business/update Update Business Details
     * @apiName UpdateBusiness
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/business/update
     * @apiDescription Updates the information and details related to the business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Admin & Business Owner
     *
     * @apiParam {Number} business_id The id of the business to update.
     * @apiParam {String} business_name The name of the business.
     * @apiParam {String} business_address The address of the business.
     * @apiParam {String} industry The type of industry of the business.
     * @apiParam {String} facebook_url The facebook page of the business.
     * @apiParam {String} timezone The timezone of the business.
     * @apiParam {String} time_open The opening time of the business.
     * @apiParam {String} time_close The closing time of the business.
     * @apiParam {Number} queue_limit The maximum number of priority numbers to give.
     * @apiParam {Boolean} terminal_specific_issue A flag to identify if terminals can only call numbers they issued.
     * @apiParam {Boolean} sms_current_number The number linked to the business for SMS capabilities.
     * @apiParam {String} sms_1_ahead .
     * @apiParam {String} sms_5_ahead .
     * @apiParam {String} sms_10_ahead .
     * @apiParam {String} sms_blank_ahead .
     * @apiParam {String} input_sms_field .
     * @apiParam {Boolean} allow_remote A flag to identify if the business allows remote queuing.
     * @apiParam {Number} remote_limit The maximum number of priority numbers allowed for remote queuing.
     * @apiParam {String} sms_gateway The sms gateway of the business.
     * @apiParam {String} frontline_sms_url The URL of the Frontline SMS mapped to the business.
     * @apiParam {String} frontline_sms_api_key The api key of the Frontline SMS mapped to the business.
     * @apiParam {String} twilio_account_sid The account id of the business to Twilio.
     * @apiParam {String} twilio_auth_token The unique token of the business to use Twilio.
     * @apiParam {String} twilio_phone_number The phone number linked to Twilio.
     *
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "status": 1
     *      }
     *
     * @apiError (Error) {String} NoBusinessFound No businesses were found using the param <code>business_id</code>.
     * @apiError (Error) {String} BusinessAlreadyExists There is already an existing business with the same <code>business_name</code> and <code>business_address</code>.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "NoBusinessFound"
     *     }
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "BusinessAlreadyExists"
     *     }
     */
    public function putUpdate(){
      $business_id = Input::get('business_id');
      if (Business::businessExistsByBusinessId($business_id)) {
        $business_data = array(
          'business_name' => Input::get('business_name'),
          'business_address' => Input::get('business_address'),
          'industry' => Input::get('industry'),
          'facebook_url' => Input::get('facebook_url'),
          'timezone' => Input::get('timezone'),
          'time_open' => Input::get('time_open'),
          'time_close' => Input::get('time_close'),
          'queue_limit' => Input::get('queue_limit'),
          'terminal_specific_issue' => Input::get('terminal_specific_issue'),
          'sms_current_number' => Input::get('sms_current_number'),
          'sms_1_ahead' => Input::get('sms_1_ahead'),
          'sms_5_ahead' => Input::get('sms_5_ahead'),
          'sms_10_ahead' => Input::get('sms_10_ahead'),
          'sms_blank_ahead' => Input::get('sms_blank_ahead'),
          'input_sms_field' => Input::get('input_sms_field'),
          'allow_remote' => Input::get('allow_remote'),
          'remote_limit' => Input::get('remote_limit'),
          'sms_gateway' => Input::get('sms_gateway'),
          'frontline_sms_url' => Input::get('frontline_sms_url'),
          'frontline_sms_api_key' => Input::get('frontline_sms_api_key'),
          'twilio_account_sid' => Input::get('twilio_account_sid'),
          'twilio_auth_token' => Input::get('twilio_auth_token'),
          'twilio_phone_number' => Input::get('twilio_phone_number'),
        );
        $business = Business::find($business_id);
        if ($this->validateBusinessNameBusinessAddress($business, $business_data)) {
          $business->name = $business_data['business_name'];
          $business->local_address = $business_data['business_address'];
          $business->industry = $business_data['industry'];
          $business->fb_url = $business_data['facebook_url'];
          $business->timezone = $business_data['timezone']; //ARA Added timezone property
          $time_open_arr = Helper::parseTime($business_data['time_open']);
          $business->open_hour = $time_open_arr['hour'];
          $business->open_minute = $time_open_arr['min'];
          $business->open_ampm = $time_open_arr['ampm'];
          $time_close_arr = Helper::parseTime($business_data['time_close']);
          $business->close_hour = $time_close_arr['hour'];
          $business->close_minute = $time_close_arr['min'];
          $business->close_ampm = $time_close_arr['ampm'];
          $business->queue_limit = $business_data['queue_limit']; /* RDH Added queue_limit to Edit Business Page */
          $business->save();

          //ARA For queue settings terminal-specific numbers
          $this->getQueueSettingsUpdate($business_id, 'number_limit', $business_data['queue_limit']);
          $this->getQueueSettingsUpdate($business_id, 'terminal_specific_issue', $business_data['terminal_specific_issue']);
          $this->getQueueSettingsUpdate($business_id, 'sms_current_number', $business_data['sms_current_number']);
          $this->getQueueSettingsUpdate($business_id, 'sms_1_ahead', $business_data['sms_1_ahead']);
          $this->getQueueSettingsUpdate($business_id, 'sms_5_ahead', $business_data['sms_5_ahead']);
          $this->getQueueSettingsUpdate($business_id, 'sms_10_ahead', $business_data['sms_10_ahead']);
          $this->getQueueSettingsUpdate($business_id, 'sms_blank_ahead', $business_data['sms_blank_ahead']);
          $this->getQueueSettingsUpdate($business_id, 'input_sms_field', $business_data['input_sms_field']);
          $this->getQueueSettingsUpdate($business_id, 'allow_remote', $business_data['allow_remote']);
          $this->getQueueSettingsUpdate($business_id, 'remote_limit', $business_data['remote_limit']);

          //sms settings
          $sms_api_data = [];
          $sms_gateway_api = NULL;
          if ($business_data['sms_gateway'] == 'frontline_sms') {
            $sms_api_data = [
              'frontline_sms_url' => $business_data['frontline_sms_url'],
              'frontline_sms_api_key' => $business_data['frontline_sms_api_key'],
            ];
            $sms_gateway_api = serialize($sms_api_data);
          }
          elseif ($business_data['sms_gateway'] == 'twilio') {
            if ($business_data['twilio_account_sid'] == TWILIO_ACCOUNT_SID &&
              $business_data['twilio_auth_token'] == TWILIO_AUTH_TOKEN &&
              $business_data['twilio_phone_number'] == TWILIO_PHONE_NUMBER
            ) {
              $business_data['sms_gateway'] = NULL;
              $sms_gateway_api = NULL;
            }
            else {
              $sms_api_data = [
                'twilio_account_sid' => $business_data['twilio_account_sid'],
                'twilio_auth_token' => $business_data['twilio_auth_token'],
                'twilio_phone_number' => $business_data['twilio_phone_number'],
              ];
              $sms_gateway_api = serialize($sms_api_data);
            }
          }
          $this->getQueueSettingsUpdate($business['business_id'], 'sms_gateway', $business_data['sms_gateway']);
          $this->getQueueSettingsUpdate($business['business_id'], 'sms_gateway_api', $sms_gateway_api);
          $business = Business::fetchBusinessDetails($business_id);
          return json_encode([
            'success' => 1,
            'business' => $business
          ]);
        }
        else {
          return json_encode(array(
            'err_code' => 'BusinessAlreadyExists'
          ));
        }
      }
      else {
        return json_encode(array(
          'err_code' => 'NoBusinessFound'
        ));
      }
    }

    private function validateBusinessNameBusinessAddress($dbBusiness, $business_data = array()) {
        if ($dbBusiness->name != $business_data['business_name'] || $dbBusiness->local_address != $business_data['business_address']){
            $row = Business::businessExistsByNameByAddress($business_data['business_name'], $business_data['business_address']);
            if(!count($row)){
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

  private function getQueueSettingsUpdate($business_id, $field, $value){
    $first_branch = Branch::where('business_id', '=', $business_id)->first();
    $first_service = Service::where('branch_id', '=', $first_branch->branch_id)->first();
    if(QueueSettings::serviceExists($first_service->service_id)){
      QueueSettings::updateQueueSetting($first_service->service_id, $field, $value);
    }else{
      QueueSettings::createQueueSetting([
        'service_id' => $first_service->service_id,
        'date' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
        $field => $value
      ]);
    }
    return json_encode(['success' => 1]);
  }

}