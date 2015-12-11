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

    public static function getUsersByRange($start_date, $end_date)
    {
        $temp_start_date = date("Y/m/d", $start_date);
        $temp_end_date = date("Y/m/d", $end_date);
        return User::where('registration_date', '>=', $temp_start_date)->where('registration_date', '<', $temp_end_date)->get();
    }
    public static function checkFBUser($fb_id)
    {
        return User::where('fb_id', '=', $fb_id)->exists();
    }

    public static function getUserIdByFbId($fb_id)
    {
        return User::where('fb_id', '=', $fb_id)->select(array('user_id'))->first()->user_id;
    }

    public static function searchByEmail($email){
        $user =  User::where('verified', '=', 1)
            ->where('email', '=', $email )
            ->select('user_id', 'first_name', 'last_name', 'email')
            ->first();
        return $user ? $user->toArray() : null;
    }
}