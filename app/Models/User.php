<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 19/11/15
 * Time: 3:57 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    public static function getUserByUserId($user_id)
    {
        return User::where('user_id', '=', $user_id)->get()->first();
    }

    public static function saveFBDetails($data)
    {
        if (!User::checkFBUser($data['fb_id'])) {
            User::insert($data);
            //Notifier::sendSignupEmail($data['email'], $data['first_name'] . ' ' . $data['last_name']);
        }
    }

    public static function checkFBUser($fb_id)
    {
        return User::where('fb_id', '=', $fb_id)->exists();
    }

    public static function getUserIdByFbId($fb_id)
    {
        return User::where('fb_id', '=', $fb_id)->select(array('user_id'))->first()->user_id;
    }

    public static function first_name($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->first_name;
    }

    public static function last_name($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->last_name;
    }

    public static function full_name($user_id)
    {
        return User::first_name($user_id) . ' ' . User::last_name($user_id);
    }

    public static function phone($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->phone;
    }

    public static function email($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->email;
    }

    public static function local_address($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->local_address;
    }

    public static function gender($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->gender;
    }

    public static function nationality($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->nationality;
    }

    public static function civil_status($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->civil_status;
    }

    public static function birthdate($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->birthdate;
    }

    public static function age($user_id)
    {
        $birthdate = User::birthdate($user_id);
        if ($birthdate) {
            return Helper::getAge($birthdate);
        } else {
            return null;
        }
    }

    public static function gcmToken($user_id)
    {
        return User::where('user_id', '=', $user_id)->first()->gcm_token;
    }

    public static function getUsersByRange($start_date, $end_date)
    {
        $temp_start_date = date("Y/m/d", $start_date);
        $temp_end_date = date("Y/m/d", $end_date);
        return User::where('registration_date', '>=', $temp_start_date)->where('registration_date', '<', $temp_end_date)->get();
    }
}