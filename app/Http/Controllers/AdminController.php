<?php

namespace App\Http\Controllers;

use App\Models\Helper;
use App\Models\Business;
use App\Models\UserBusiness;
use App\Models\User;
use App\Models\Analytics;
use App\Models\Admin;
use Illuminate\Support\Facades\Input;

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 5/21/15
 * Time: 3:37 PM
 */
class AdminController extends Controller
{

    /**
     * @api {get} /admin/stats/{date_start}/{date_end} Retrieve Business Analytics.
     * @apiName AdminAnalytics
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/stats/20150101/20150130
     * @apiDescription Retrieve business information
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {Date} date_start Start date in which to query businesses. Format should be <code>Ymd</code>.
     * @apiParam {Date} end_date End date in which to query businesses. Format should be <code>Ymd</code>.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     * @apiSuccess (200) {Number} business_count Business count.
     * @apiSuccess (200) {Array} business_information Array of business information.
     * @apiSuccess (200) {String} business_information.business_name Name of business.
     * @apiSuccess (200) {String} business_information.name Name of business owner.
     * @apiSuccess (200) {String} business_information.email Address of business owner.
     * @apiSuccess (200) {String} business_information.phone Contact number of business owner.
     * @apiSuccess (200) {Number} users_count User count.
     * @apiSuccess (200) {Array} users_information Array of user details.
     * @apiSuccess (200) {String} users_information.first_name First name of user.
     * @apiSuccess (200) {String} users_information.last_name Last name of user.
     * @apiSuccess (200) {String} users_information.email Email of user.
     * @apiSuccess (200) {String} users_information.phone Contact number of user.
     * @apiSuccess (200) {Array} business_numbers Business queue information.
     * @apiSuccess (200) {Number} business_numbers.issued_numbers Issued numbers count.
     * @apiSuccess (200) {Number} business_numbers.called_numbers Called numbers count.
     * @apiSuccess (200) {Number} business_numbers.served_numbers Served numbers count.
     * @apiSuccess (200) {Number} business_numbers.dropped_numbers Dropped numbers count.
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
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code InvalidInput Invalid input/s found.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
     *     }
     */
    public function getBusinessnumbers($start_date = null, $end_date = null)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            if (is_null($start_date) || is_null($end_date) || !Helper::is_Ymd($start_date) || !Helper::is_Ymd($end_date)) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'InvalidInput'
                ));
            }

            $businesses_count = 0;
            $users_count = 0;
            $users_information = [];
            $businesses_information = [];
            $end_date_1 = date('Ymd', strtotime($end_date . "+1 days"));

            $businesses = Business::getBusinessByRangeYmd($start_date, $end_date_1);
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
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }

    /**
     * @api {get} /admin/list Retrieve admin emails.
     * @apiName AdminList
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/list
     * @apiDescription Retrieve admin emails.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     *
     * @apiSuccess (200) {Number} success Process success/fail flag.
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
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code Invalid Invalid input.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
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
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }

    /**
     * @api {post} /admin/add/{email} Add Admin.
     * @apiName AdminRegistration
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/add/admin@example.com
     * @apiDescription Register admin information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     * @apiParam {String} email Unique email of admin.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code InvalidEmail The email entered has an invalid format..
     * @apiError (Error) {String} err_code SomethingWentWrong Something went wrong while adding the user. Please try again.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
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
                        'err_code' => 'InvalidEmail'
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
                    'err_code' => 'SomethingWentWrong'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }

    /**
     * @api {delete} /admin/delete/{email} Delete Admin.
     * @apiName AdminDeletion
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/delete/admin@example.com
     * @apiDescription Delete admin information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     * @apiParam {String} email Unique email of admin.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code InvalidEmail The email entered has an invalid format..
     * @apiError (Error) {String} err_code SomethingWentWrong Something went wrong while adding the user. Please try again.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
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
                        'err_code' => 'InvalidEmail'
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
                    'err_code' => 'SomethingWentWrong'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }

    /**
     * @api {post} /admin/features/update/{business_id} Update Business Features.
     * @apiName UpdateBusinessFeatures
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/features/update/123123
     * @apiDescription Update business features information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {String} business_id Unique ID of business to add this feature to.
     * @apiParam {Object} data JSON object to be serialized.
     * @apiParam {Boolean} data.allow_sms Allow SMS flag.
     * @apiParam {Boolean} data.queue_forwarding Queue forwarding flag.
     * @apiParam {Number} data.terminal_users Terminal users count.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code BusinessNotFound No business matched using <code>business_id</code>
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
     *     }
     */
    public function postSaveFeatures($business_id = null)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            $business = Business::getBusinessByBusinessId($business_id);
            if (!is_null($business)) {
                $data = Input::all();
                Business::saveBusinessFeatures($business_id, $data);
                return json_encode(array(
                    'success' => 1
                ));
            } else {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'BusinessNotFound'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }

    /**
     * @api {get} /admin/features/{business_id} Retrieve Business Features.
     * @apiName RetrieveBusinessFeatures
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/admin/features/123
     * @apiDescription Retrieve business features information.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Admin
     *
     * @apiParam {String} business_id Unique ID of business to retrieve.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     * @apiSuccess (200) {Object} features Business features. This object should be serialized.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *          "features": {
     *              "terminal_users": "3",
     *              "allow_sms": "true"
     *          }
     *      }
     *
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code BusinessNotFound No business matched using <code>business_id</code>
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
     *     }
     */
    public function getBusinessFeatures($business_id = null)
    {
        // TODO check permissions of API user, add authentication here.
        if (true) {
            $business = Business::getBusinessByBusinessId($business_id);
            if (!is_null($business)) {
                return json_encode(['success' => 1, 'features' => $business->business_features]);
            } else {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'BusinessNotFound'
                ));
            }
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }

}