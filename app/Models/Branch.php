<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/16/2015
 * Time: 3:31 PM
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    protected $table = 'branch';
    protected $primaryKey = 'branch_id';
    public $timestamps = false;

    public static function businessId($branch_id)
    {
        return Branch::where('branch_id', '=', $branch_id)->select(array('business_id'))->first()->business_id;
    }

    public static function getBranchesByBusinessId($business_id){
        return Branch::where('business_id', '=', $business_id)->get();
    }

    public static function getFirstBranchOfBusiness($business_id){
        return Branch::where('business_id', '=', $business_id)->first();
    }
}