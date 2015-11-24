<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 3:39 PM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model{

    protected $table = 'user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;


    public static function saveFBDetails($data)
    {
        if (!User::checkFBUser($data['fb_id']))
        {
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
}