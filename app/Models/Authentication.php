<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 3:35 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Authentication extends Model{

    public static function register($data){
        $post = json_decode(json_encode($data));
        $response = FB::VerifyFB($post->accessToken);
        if ($response->getGraphUser()) {
            $values = array(
                'fb_id' => $post->fb_id,
                'fb_url' => $post->fb_url,
                'first_name' => $post->first_name,
                'last_name' => $post->last_name,
                'email' => $post->email,
                'gender' => $post->gender,
            );

            if(!User::checkFBUser($post->fb_id)){
                User::saveFBDetails($values);
            }

            return Authentication::login($values['fb_id']);
        }
    }

    public static function login($fb_id){
        if(User::checkFBUser($fb_id)){
            //@todo generate access token
            $accessToken = '';
            Session::put('accessToken', $accessToken);
            return json_encode(['success' => 1, 'accessToken' => $accessToken]);
        }else{
            return json_encode(['success' => 0, 'error' => 'Please sign up to Featherq']);
        }
    }

    public static function logout(){
        Session::forget('accessToken');
        return json_encode(['success' => 1]);
    }



}