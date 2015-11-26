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

  public static function getUserByUserId($user_id){
    return User::where('user_id', '=', $user_id)->get()->first();
  }

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