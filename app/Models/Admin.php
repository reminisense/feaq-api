<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 5/21/15
 * Time: 4:11 PM
 */
class Admin extends Model
{

    public static function csvUrl()
    {
        return base_path('app/Constants/FeatherqAdmins.csv');
    }

    public static function getAdmins()
    {
        try {
            $file = fopen(Admin::csvUrl(), 'r');
            $emails = fgetcsv($file);
            fclose($file);
            return $emails;
        } catch (Exception $e) {
            return [];
        }
    }

    public static function isAdmin($user_id = null)
    {
        try {
            $user_id = $user_id ? $user_id : Helper::userId(); //$user_id = $user_id != NULL ? $user_id : Helper::userId(); // PAG changed because this will be true if $user_id = 0 which is supposed to be false too
            $emails = Admin::getAdmins();
            return in_array(User::email($user_id), $emails);
        } catch (Exception $e) {
            return false;
        }
    }
}