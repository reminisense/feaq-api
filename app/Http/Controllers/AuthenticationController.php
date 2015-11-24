<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 3:35 PM
 */

namespace App\Http\Controllers;

use App\Models\Authentication;
use Illuminate\Support\Facades\Input;

class AuthenticationController extends Controller
{

    public function login(){
        return Authentication::login(Input::get('fb_id'));
    }

    public function logout(){
        return Authentication::logout();
    }

    public function register(){
        return Authentication::register(Input::all());
    }
}