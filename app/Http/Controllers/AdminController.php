<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\UserBusiness;
use App\Models\User;
use App\Models\Analytics;

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 5/21/15
 * Time: 3:37 PM
 */
class AdminController extends Controller
{

    /**
     * @api {get} analytics/business/{date_start}/{date_end} Retrieve business analytics
     * @apiName
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/analytics/business/{date_start}/{date_end}
     * @apiDescription Retrieve business information
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {Date} date_start Start date in which to query businesses. Format should be Ymd.
     * @apiParam {Date} end_date End date in which to query businesses. Format should be Ymd.
     *
     * @apiSuccess (200) {Boolean} success Process success/fail flag.
     * @apiSuccess (200) {Number} business_count Business count.
     * @apiSuccess (200) {Array} business_information Array of business information.
     * @apiSuccess (200) {Number} users_count User count.
     * @apiSuccess (200) {Array} users_information Array of user details.
     * @apiSuccess (200) {Array} business_numbers Business queue information.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "businesses_count": 2,
     *          "businesses_information": [
     *              {
     *                  "business_name": "Supply Depot",
     *                  "name": "Gii Amores",
     *                  "email": "giandexamores@example.com",
     *                  "phone": "+639221234567"
     *              },
     *              {
     *                  "business_name": "Tintins hard caps",
     *                  "name": "Roman Makarov",
     *                  "email": "rodel.maranon@gmail.com",
     *                  "phone": "023124212512521"
     *              }
     *          ],
     *          "users_count": 1,
     *          "users_information": [
     *              {
     *                  "first_name": "Gii",
     *                  "last_name": "Amores",
     *                  "email": "giandexamores@example.com",
     *                  "phone": "+639221234567"
     *              }
     *          ],
     *          "business_numbers": {
     *              "issued_numbers": 320,
     *              "called_numbers": 306,
     *              "served_numbers": 84,
     *              "dropped_numbers": 1
     *          }
     *      }
     *
     * @apiError (Error) {String} Unauthorized User does not have admin rights.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "Unauthorized"
     *     }
     */
    public function getBusinessnumbers($start_date, $end_date)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            $businesses_count = 0;
            $users_count = 0;
            $users_information = [];
            $businesses_information = [];
            $end_date = date('Ymd', strtotime($end_date . "+1 days"));

            $businesses = Business::getBusinessByRangeYmd($start_date, $end_date);
            if ($businesses) {
                $businesses_count = count($businesses);
                for ($i = 0; $i < $businesses_count; $i++) {
                    $user = UserBusiness::getUserByBusinessId($businesses[$i]->business_id);
                    if (!is_null($user)) {
                        array_push($businesses_information,
                            [
                                'business_name' => $businesses[$i]->name,
                                'name' => $user->first_name . ' ' . $user->last_name,
                                'email' => $user->email,
                                'phone' => $user->phone
                            ]
                        );
                    } else {
                        array_push($businesses_information,
                            [
                                'business_name' => $businesses[$i]->name
                            ]
                        );
                    }
                }
            }

            $users = User::getUsersByRangeYmd($start_date, $end_date);
            if ($users) {
                $users_count = count($users);
                for ($i = 0; $i < $users_count; $i++) {
                    array_push($users_information,
                        [
                            'first_name' => $users[$i]->first_name,
                            'last_name' => $users[$i]->last_name,
                            'email' => $users[$i]->email,
                            'phone' => $users[$i]->phone
                        ]
                    );
                }
            }

            $business_numbers = [
                'issued_numbers' => Analytics::countBusinessNumbersYmd($start_date, $end_date, 0),
                'called_numbers' => Analytics::countBusinessNumbersYmd($start_date, $end_date, 1),
                'served_numbers' => Analytics::countBusinessNumbersYmd($start_date, $end_date, 2),
                'dropped_numbers' => Analytics::countBusinessNumbersYmd($start_date, $end_date, 3)
            ];


            return json_encode(array(
                'success' => 1,
                'businesses_count' => $businesses_count,
                'businesses_information' => $businesses_information,
                'users_count' => $users_count,
                'users_information' => $users_information,
                'business_numbers' => $business_numbers
            ));
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'Unauthorized'
            ));
        }
    }

}