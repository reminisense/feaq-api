<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 3:35 PM
 */

namespace App\Models;

use Facebook\Facebook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Authentication extends Model{

    public static function register($data){
        $post = json_decode(json_encode($data));
        $response = isset($post->accessToken) && isset($post->fb_id)? FB::VerifyFB($post->accessToken, $post->fb_id) : null;
        if($response){
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
                Watchdog::createRecord(['action_type' => 'authentication', 'value' => serialize(['action'=> 'signup', 'success' => true,'click_source' => $post->click_source])]); //save to watchdog the source of login
            }

            return Authentication::login($values['fb_id'], $post->click_source);
        }else{
            return json_encode(['success' => 0, 'error' => 'Facebook authentication failed']);
        }
    }

    public static function login($fb_id, $click_source){
        if(User::checkFBUser($fb_id)){
            //@todo generate access token
            $accessToken = '';
            Session::put('accessToken', $accessToken);
            Watchdog::createRecord(['action_type' => 'authentication', 'value' => serialize(['action'=> 'login', 'success' => true, 'click_source' => $click_source])]); //save to watchdog the source of login
            return json_encode(['success' => 1, 'accessToken' => $accessToken]);
        }else{
            Watchdog::createRecord(['action_type' => 'authentication', 'value' => serialize(['action'=> 'login', 'success' => false, 'click_source' => $click_source])]); //save to watchdog the source of login
            return json_encode(['success' => 0, 'error' => 'Please sign up to Featherq']);
        }
    }

    public static function logout(){
        Session::forget('accessToken');
        return json_encode(['success' => 1]);
    }



}