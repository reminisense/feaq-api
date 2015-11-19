<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 19/11/15
 * Time: 3:57 PM
 */

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{

  public function getProfile($user_id) {
    $user = User::getUserByUserId($user_id);
    return json_encode(array(
      'user_id' => $user_id,
      'email' => $user->email,
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'phone' => $user->phone,
      'local_address' => $user->local_address,
    ));
  }

}