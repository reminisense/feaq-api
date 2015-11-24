<?php
/**
 * Created by PhpStorm.
 * User: polljii
 * Date: 24/11/15
 * Time: 3:56 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastNumbers extends Model {

  protected $table = 'broadcast_numbers';
  protected $primaryKey = 'business_id';
  public $timestamps = false;


}