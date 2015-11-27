<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 3:45 PM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class FB extends Model{
    public static function VerifyFB($accessToken, $fb_id)
    {
        // Call Facebook and let them verify if the information sent by the user
        // is the same with the ones in their database.
        // This will save us from the exploit of a post request with bogus details
        $fb = new Facebook(array(
            'app_id' => '1577295149183234',
            'app_secret' => '23a15a243f7ce66a648ec6c48fa6bee9',
            'default_graph_version' => 'v2.4',
        ));
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->sendRequest('GET', '/me', array('access_token' => $accessToken, 'id' => $fb_id)); // Use the access token retrieved by JS login
            $user = $response->getGraphUser();
            if($user['id'] == $fb_id){
                return true;
            }
            return false;
        } catch (FacebookResponseException $e) {
            //return json_encode(array('message' => $e->getMessage()));
            Authentication::logout();
        } catch (FacebookSDKException $e) {
            //return json_encode(array('message' => $e->getMessage()));
            Authentication::logout();
        }
        return null;
    }
}