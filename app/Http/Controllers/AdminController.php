<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\UserBusiness;
use App\Models\User;
use App\Models\Analytics;
use App\Models\Admin;

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 5/21/15
 * Time: 3:37 PM
 */
class AdminController extends Controller
{

    /**
     * @api {get} /admin/analytics/{date_start}/{date_end} Retrieve business analytics.
     * @apiName Admin Analytics
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/analytics/20150101/20150130
     * @apiDescription Retrieve business information
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {Date} date_start Start date in which to query businesses. Format should be Ymd.
     * @apiParam {Date} end_date End date in which to query businesses. Format should be Ymd.
     *
     * @apiSuccess (200) {Boolean} success Process success flag.
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
     * @apiError (Error) {Boolean} success Process fail flag.
     * @apiError (Error) {String} err_code Unauthorized User does not have admin rights.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "Unauthorized"
     *     }
     */
    public function getBusinessnumbers($start_date = null, $end_date = null)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            // FIXME common method to handle date format checking
            if (is_null($start_date) || is_null($end_date)) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'Invalid input.'
                ));
            }

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

    /**
     * @api {get} /admin/list Retrieve admin emails.
     * @apiName Admin List
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/list
     * @apiDescription Retrieve admin information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     *
     * @apiSuccess (200) {Boolean} success Process success/fail flag.
     * @apiSuccess (200) {Array} admins Array containing the emails of administrators.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "admins": [
     *              example1@example.com,
     *              admin@example.com
     *          ]
     *      }
     *
     * @apiError (Error) {Boolean} success Process fail flag.
     * @apiError (Error) {String} err_code Unauthorized User does not have admin rights.
     * @apiError (Error) {String} err_code Invalid Invalid input.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "Unauthorized"
     *     }
     */
    public function getAdmins()
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            return json_encode(['success' => true, 'admins' => Admin::getAdmins()]);
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'Unauthorized'
            ));
        }
    }

    /**
     * @api {post} /admin/add/{email} Add admin.
     * @apiName Admin Registration
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/add/admin@example.com
     * @apiDescription Register admin information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     *
     * @apiSuccess (200) {Boolean} success Process success flag.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Boolean} success Process fail flag.
     * @apiError (Error) {String} err_code Unauthorized User does not have admin rights.
     * @apiError (Error) {String} err_code Invalid Invalid input.
     * @apiError (Error) {String} err_code Unknown Failed to add admin.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "Unauthorized"
     *     }
     */
    public function addAdmin($email = null)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {

            try {
                if (is_null($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return json_encode(array(
                        'success' => 0,
                        'err_code' => 'Invalid input.'
                    ));
                }

                $emails = Admin::getAdmins();
                if (!in_array($email, $emails)) {
                    $emails[] = $email;
                    $file = fopen(Admin::csvUrl(), 'w');
                    fputcsv($file, $emails, ',');
                    fclose($file);
                }
                return json_encode(array(
                    'success' => 1
                ));
            } catch (Exception $e) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'Failed to add to admin list'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'Unauthorized'
            ));
        }
    }

    /**
     * @api {delete} /admin/delete/{email} Delete admin.
     * @apiName Admin Deletion
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/delete/admin@example.com
     * @apiDescription Delete admin information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     *
     * @apiSuccess (200) {Boolean} success Process success flag.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Boolean} success Process fail flag.
     * @apiError (Error) {String} err_code Unauthorized User does not have admin rights.
     * @apiError (Error) {String} err_code Invalid Invalid input.
     * @apiError (Error) {String} err_code Unknown Failed to add admin.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "Unauthorized"
     *     }
     */
    public static function removeAdmin($email)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            try {
                if (is_null($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return json_encode(array(
                        'success' => 0,
                        'err_code' => 'Invalid input.'
                    ));
                }

                $emails = Admin::getAdmins();
                if (in_array($email, $emails)) {
                    unset($emails[array_search($email, $emails)]);
                    $file = fopen(Admin::csvUrl(), 'w');
                    fputcsv($file, $emails, ',');
                    fclose($file);
                }
                return json_encode(array(
                    'success' => 1
                ));
            } catch (Exception $e) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'Failed to add to admin list'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'Unauthorized'
            ));
        }
    }

}