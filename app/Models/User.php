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

}