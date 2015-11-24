<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 24/11/15
 * Time: 3:55 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastSettings extends Model {

  protected $table = 'broadcast_settings';
  protected $primaryKey = 'business_id';
  public $timestamps = false;

  public static function fetchAllSettingsByBusiness($business_id = 0) {
    return BroadcastSettings::where('business_id', '=', $business_id)->first();
  }


}