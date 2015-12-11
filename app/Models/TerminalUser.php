<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 12/7/2015
 * Time: 1:23 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TerminalUser extends Model{

    protected $table = 'terminal_user';
    protected $primaryKey = 'terminal_user_id';
    public $timestamps = false;

    public static function getAssignedUsers($terminal_id){
        return TerminalUser::where('terminal_user.status', '=', 1)
            ->where('terminal_user.terminal_id', '=', $terminal_id)
            ->join('user', 'user.user_id', '=', 'terminal_user.user_id')
            ->select('terminal_user.*', 'user.first_name' , 'user.last_name')
            ->get()
            ->toArray();
    }

    public static function assignTerminalUser($user_id, $terminal_id){
        if(!Terminal::where('terminal_id', '=', $terminal_id)->exists()) {return json_encode(['success' => 0, 'err_code' => 'NoTerminalFound']);}
        if(!User::where('user_id', '=', $user_id)->exists()){return json_encode(['success' => 0, 'err_code' => 'NoUserFound']);}

        if(TerminalUser::terminalUserExists($user_id, $terminal_id)){ TerminalUser::updateTerminalUserStatus($user_id, $terminal_id, 1);
        }else{ TerminalUser::createTerminalUser($user_id, $terminal_id);}

        $business_id = Business::getBusinessIdByTerminalId($terminal_id);
        $business = Business::getBusinessDetails($business_id);
        return json_encode(['success' => 1, 'business' => $business]);

    }

    public static function unassignTerminalUser($user_id, $terminal_id){
        if(!Terminal::where('terminal_id', '=', $terminal_id)->exists()) {return json_encode(['success' => 0, 'err_code' => 'NoTerminalFound']);}
        if(!User::where('user_id', '=', $user_id)->exists()){return json_encode(['success' => 0, 'err_code' => 'NoUserFound']);}

        TerminalUser::updateTerminalUserStatus($user_id, $terminal_id, 0);
        $business_id = Business::getBusinessIdByTerminalId($terminal_id);
        $business = Business::getBusinessDetails($business_id);
        return json_encode(['success' => 1, 'business' => $business]);
    }

    public static function terminalUserExists($user_id, $terminal_id){
        return TerminalUser::where('user_id', '=', $user_id)->where('terminal_id', '=', $terminal_id)->first();
    }

    public static function createTerminalUser($user_id, $terminal_id){
        $date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $values = [
            'user_id' => $user_id,
            'terminal_id' => $terminal_id,
            'status' => 1,
            'date' => $date
        ];
        return TerminalUser::insertGetId($values);
    }

    public static function updateTerminalUserStatus($user_id, $terminal_id, $status = 0){
        TerminalUser::where('user_id', '=', $user_id)->where('terminal_id', '=', $terminal_id)->update(['status' => $status]);
    }

    public static function hookedTerminal($terminal_id) {
        if (DB::table('terminal_manager')->where('terminal_id', '=', $terminal_id)->first()) {
            return DB::table('terminal_manager')->orderBy('login_id', 'desc')->select('in_out')->where('terminal_id', '=', $terminal_id)->first()->in_out;
        }
    }

    public static function getLatestLoginIdOfTerminal($terminal_id) {
        return DB::table('terminal_manager')->orderBy('login_id', 'desc')->select('login_id')->where('terminal_id', '=', $terminal_id)->first()->login_id;
    }

  public static function deleteUserByTerminalId($terminal_id) {
    TerminalUser::where('terminal_id', '=', $terminal_id)->delete();
  }

}