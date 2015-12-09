<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 12/9/2015
 * Time: 1:07 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model{

    protected $table = 'user_rating';
    protected $primaryKey = 'user_rating_id';
    public $timestamps = false;

    public static function rateUser($date, $business_id, $rating, $user_id, $terminal_user_id, $action){

        $data= [
            'business_id' => $business_id,
            'rating' => $rating,
            'user_id' => $user_id,
            'terminal_user_id' => $terminal_user_id,
            'action' => $action,
            'date' => $date
        ];

        UserRating::saveRatingUser($data);

    }

    public static function saveRatingUser($data){
       UserRating::insert($data);
    }

}