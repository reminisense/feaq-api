<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 11/23/2015
 * Time: 7:59 PM
 */

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Support\Facades\Input;

class LandingPageController extends Controller
{

    /**
     * @api {post} business/search Search Businesses.
     * @apiName Search
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/business/search
     * @apiDescription Fetch businesses according to given search parameters.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {String} [keyword] The keyword used to search for the business.
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
     *     [{
     *          "business_id": 9,
     *          "business_name": "ABCDEF",
     *          "local_address": "Cebu City, Central Visayas, Philippines",
     *          "time_open": "12:00 AM",
     *          "time_close": "8:00 AM",
     *          "waiting_time": "light",
     *          "last_number_called": "none",
     *          "next_available_number": 1,
     *          "last_active": 270.33333333333,
     *          "card_bool": false
     *     },
     *     {
     *          "business_id": 10,
     *          "business_name": "Logitech Gaming",
     *          "local_address": "Cebu City, Central Visayas, Philippines",
     *          "time_open": "8:00 AM",
     *          "time_close": "10:00 PM",
     *          "waiting_time": "light",
     *          "last_number_called": "none",
     *          "next_available_number": 1,
     *          "last_active": 138.33333333333,
     *          "card_bool": false
     *      }]
     *
     */
    public function search() {
        $keywords = Input::all();
        return Business::searchBusiness($keywords);
    }
}