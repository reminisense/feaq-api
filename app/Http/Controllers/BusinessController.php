<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/17/2015
 * Time: 10:54 AM
 */

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Analytics;

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
    public function search()
    {
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
    public function searchSuggest($keyword)
    {
        $businesses = Business::searchSuggest($keyword);
        return json_encode($businesses);
    }

    /**
     * @api {get} analytics/business/{business_id}{date_start}/{date_end} Retrieve business analytics
     * @apiName Business analytics retrieval
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/analytics/business/1/20151101/20151207
     * @apiDescription Retrieve business analytics information
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {Number} business_id Unique ID of business to retrieve.
     * @apiParam {Date} date_start Start date in which to query businesses. Format should be Ymd.
     * @apiParam {Date} end_date End date in which to query businesses. Format should be Ymd.
     *
     * @apiSuccess (200) {Boolean} successProcess success/fail flag.
     * @apiSuccess (200) {Number} total_numbers_issued Business count.
     * @apiSuccess (200) {Number} total_numbers_called Array of business information.
     * @apiSuccess (200) {Number} total_numbers_served User count.
     * @apiSuccess (200) {Number} total_numbers_dropped Array of user details.
     * @apiSuccess (200) {String} average_time_called Business queue information.
     * @apiSuccess (200) {String} average_time_called Business queue information.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "remaining_count": 0,
     *          "total_numbers_issued": 2,
     *          "total_numbers_called": 123,
     *          "total_numbers_served": 1,
     *          "total_numbers_dropped": 0,
     *          "average_time_called": "",
     *          "average_time_served": ""
     *      }
     *
     * @apiError (Error) {String} success Process fail flag.
     * @apiError (Error) {String} err_code Not Found Business ID was not found.
     * @apiError (Error) {String} err_code Invalid Invalid input.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "Business does not exist"
     *     }
     */
    public function businessAnalytics($business_id, $start_date, $end_date)
    {
        $business = Business::getBusinessByBusinessId($business_id);
        if (is_null($business)) {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'Business does not exist'
            ));
        }
        // FIXME common method to handle date format checking
        if (is_null($start_date) || is_null($end_date)) {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'Invalid input.'
            ));
        }
        $analytics = Analytics::getBusinessAnalytics($business_id, strtotime($start_date), strtotime($end_date));
        return json_encode($analytics);
    }
}