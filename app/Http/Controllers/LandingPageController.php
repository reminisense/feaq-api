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
     * @api {post} business/{search_param} Fetch businesses according to given search parameters.
     * @apiName Business Search
     * @apiGroup Business
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/business/<???>
     * @apiDescription Fetch businesses according to given search parameters.
     *
     * @apiHeader {String} access-key The unique access key sent by the client. TODO
     * @apiPermission Business Owner TODO
     *
     * @apiParam {Number} business_id The id of the business. TODO
     *
     * @apiSuccess (Success 200) {String} list of businesses matching search parameters.
     * @apiSuccessExample {Json} Success-Response: TODO
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "img_id": 72,
     *         "path": "ads\/125\/o_1a2pute0r17ns1fi91p8q1vj6ric.jpg",
     *         "weight": 19,
     *         "business_id": 125
     *       },
     *       {
     *         "img_id": 74,
     *         "path": "ads\/125\/o_1a2pute0rmt3nm7f5o10927tue.png",
     *         "weight": 21,
     *         "business_id": 125
     *       }
     *     ]
     *
     * @apiError (Error 404) {String} NoImagesFound The <code>NoImagesFound</code> is null.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "err_message": "NoImagesFound"
     *     }
     */
    public function search() {

        $search_param = Input::all();

        $country = Input::get('country');
        $industry = Input::get('industry');
        $keyword = Input::get('keyword');
        $latitude = Input::get('latitude');
        $longitude = Input::get('longitude');
        $time_open = Input::get('time_open');
        $user_timezone = Input::get('user_timezone');


        return Business::searchBusiness($search_param);
    }
}