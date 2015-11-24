<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/24/2015
 * Time: 2:22 PM
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Watchdog extends Model
{
    protected $table = 'watchdog';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    public static function createRecord($val = array()) {
        return Watchdog::insertGetId($val);
    }
}