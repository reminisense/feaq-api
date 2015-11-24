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
     * @api {post} business/search Fetch businesses.
     * @apiName Business Search
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/business/search
     * @apiDescription Fetch businesses according to given search parameters.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Business Owner
     *
     * @apiParam {String} country The country where business operates.
     * @apiParam {String} industry The industry where the business belongs to.
     * @apiParam {String} keyword The keyword to search.
     * @apiParam {Number} latitude Geolocation latitude.
     * @apiParam {Number} longitude Geolocation longitude.
     * @apiParam {Number} time_open The id of the business.
     * @apiParam {Number} time_open The id of the business.
     *
     *
     * @apiSuccess (200) {Array} list of businesses matching search parameters.
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
        // TODO need to optimize this!! very very slow. :(
        $search_param = Input::all();
        return Business::searchBusiness($search_param);
    }
}