<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/19/2015
 * Time: 11:08 AM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'service';
    protected $primaryKey = 'service_id';
    public $timestamps = false;

    public static function branchId($service_id){
        return Service::find($service_id)->branch_id;
    }

    public static function getServicesByBranchId($branch_id){
        return Service::where('branch_id', '=', $branch_id)->get();
    }

    public static function getFirstServiceOfBranch($branch_id){
        return Service::where('branch_id', '=', $branch_id)->first();
    }

    public static function getFirstServiceOfBusiness($business_id){
        $first_branch = Branch::getFirstBranchOfBusiness($business_id);
        if(is_null($first_branch)) {
            return null;
        }
        return Service::getFirstServiceOfBranch($first_branch->branch_id);
    }
}