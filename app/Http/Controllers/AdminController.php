<?php

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 5/21/15
 * Time: 3:37 PM
 */
class AdminController extends BaseController
{

    /**
     * @api {get} analytics/business/{date_start}/{date_end} Retrieve business analytics
     * @apiName
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/user/1
     * @apiDescription Gets all the information pertaining to the user.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated User
     *
     * @apiParam {Number} user_id The id of the user.
     *
     * @apiSuccess (200) {Number} user_id The id of the user.
     * @apiSuccess (200) {String} email The email address of the user.
     * @apiSuccess (200) {String} first_name The first name of the user.
     * @apiSuccess (200) {String} last_name The last name of the user.
     * @apiSuccess (200) {String} phone The phone number of the user.
     * @apiSuccess (200) {String} local_address The address of the user.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "user_id": "13",
     *       "email": "foo@example.com",
     *       "first_name": "Foo Foo",
     *       "last_name": "Example",
     *       "phone": "1234567890",
     *       "local_address": "Disneyland, Hongkong"
     *     }
     *
     * @apiError (Error) {String} UserNotFound There were no users found with the given <code>user_id</code>.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "UserNotFound"
     *     }
     */
    public function getBusinessnumbers($start_date, $end_date)
    {
        // TODO check permissions of API user

        $businesses_count = 0;
        $users_count = 0;
        $users_information = [];
        $businesses_information = [];
        $temp_date = $end_date + 86400;

        $businesses = Business::getBusinessByRange($start_date, $temp_date);
        if ($businesses) {
            $businesses_count = count($businesses);
            for ($i = 0; $i < $businesses_count; $i++) {
                $user = UserBusiness::getUserByBusinessId($businesses[$i]->business_id);
                array_push($businesses_information,
                    [
                        'business_name' => $businesses[$i]->name,
                        'name' => User::full_name($user->user_id),
                        'email' => User::email($user->user_id),
                        'phone' => User::phone($user->user_id)
                    ]
                );
            }
        }
        $users = User::getUsersByRange($start_date, $temp_date);
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
            'issued_numbers' => Analytics::countBusinessNumbers($start_date, $end_date, 0),
            'called_numbers' => Analytics::countBusinessNumbers($start_date, $end_date, 1),
            'served_numbers' => Analytics::countBusinessNumbers($start_date, $end_date, 2),
            'dropped_numbers' => Analytics::countBusinessNumbers($start_date, $end_date, 3)
        ];


        return json_encode(array(
            'success' => 1,
            'businesses_count' => $businesses_count,
            'businesses_information' => $businesses_information,
            'users_count' => $users_count,
            'users_information' => $users_information,
            'business_numbers' => $business_numbers
        ));
    }

}