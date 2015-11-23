<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 11:26 AM
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Terminal extends Model{
    protected $table = 'terminal';
    protected $primaryKey = 'terminal_id';
    public $timestamps = false;
    public static function boxRank($terminal_id) {
        return $terminal_id ? Terminal::where('terminal_id', '=', $terminal_id)->select(array('box_rank'))->first()->box_rank : 0;
    }
}