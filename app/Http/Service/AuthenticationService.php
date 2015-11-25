<?php

namespace App\Http\Service;

/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 11/25/2015
 * Time: 7:06 PM
 */
class AuthenticationService
{
    public static function authenticate($username) {
        // FIXME provide authentication implementation here.
        if(strcmp($username, 'nicogwapo') != 0) {
            return false;
        }
        return true;
    }

}