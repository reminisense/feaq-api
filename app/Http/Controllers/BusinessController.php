<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/17/2015
 * Time: 10:54 AM
 */

namespace App\Http\Controllers;

use App\Models\Business;

class BusinessController extends Controller
{
    /**
     * @api {get} /business/search
     * @apiName business search
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/business/search?keyword=&country=&industry=&time_open=&timezone=&limit=&offset
     * @apiDescription Search for businesses based on the given parameters
     *
     * @apiHeader none
     * @apiPermission none
     *
     * @apiParam {String} [keyword] The keyword used to search for the business
     * @apiParam {String} [country] The country of the business
     * @apiParam {String} [industry] The industry of the business
     * @apiParam {String} [time_open] The time the business opens
     * @apiParam {String} [timezone] The timezone of the business
     * @apiParam {Number} [limit] The number of entries to be retrieved
     * @apiParam {Number} [offset] The number where the entries retrieved will start
     *
     * @apiSuccess (Success 200) {Object} arr Array of objects with business details
     * @apiSuccess (Success 200) {Number} arr.business_id The business id of the business in the database
     * @apiSuccess (Success 200) {String} arr.business_name The name of the business
     * @apiSuccess (Success 200) {String} arr.local_address The address of the business
     * @apiSuccess (Success 200) {String} arr.time_open The time that the business opens
     * @apiSuccess (Success 200) {String} arr.time_close The time that the business closes
     * @apiSuccess (Success 200) {String} arr.waiting_time Indicates how heavy the queue is based on time it takes for the last number in the queue to be called
     * @apiSuccess (Success 200) {Number} arr.last_number_called The last number called by the business
     * @apiSuccess (Success 200) {Number} arr.next_available_number The next number that can be placed to the queue
     * @apiSuccess (Success 200) {Number} arr.last_active The number of days when the business last processed the queue
     * @apiSuccess (Success 200) {Bool} arr.card_bool Indicates if the business is active or not
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
        $arr = Business::searchBusiness($_GET);
        return json_encode($arr);
    }

    /**
     * @api {get} /business/search-suggest/{keyword}
     * @apiName business search-suggest
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/business/search-suggest/keyword
     * @apiDescription Suggests search items for businesses based on the given keyword
     *
     * @apiHeader none
     * @apiPermission none
     *
     * @apiParam {String} [keyword] The keyword used to search for the business
     *
     * @apiSuccess (Success 200) {Object} arr Array of objects with business details
     * @apiSuccess (Success 200) {String} arr.business_name The name of the business
     * @apiSuccess (Success 200) {String} arr.local_address The address of the business
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
        return json_encode(array('keywords' => $businesses));
    }
}