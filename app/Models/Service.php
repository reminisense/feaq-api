<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 1/22/15
 * Time: 5:21 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{

    protected $table = 'service';
    protected $primaryKey = 'service_id';
    public $timestamps = false;

    public static function branchId($service_id)
    {
        return Service::find($service_id)->branch_id;
    }

    public static function name($sevice_id)
    {
        return Service::find($sevice_id)->name;
    }

    /*
     * @author: ARA
     * @description: create new service
     * @return service_id
     */
    public static function createService($branch_id, $name)
    {
        $service = new Service();
        $service->name = $name;
        $service->status = 1;
        $service->branch_id = $branch_id;
        $service->save();

        return $service->service_id;
    }

    /*
     * @author: ARA
     * @description: create new service for business
     * @return service_id
     */
    public static function createBusinessService($business_id, $name)
    {
        $first_branch = Branch::getFirstBranchOfBusiness($business_id);
        return Service::createService($first_branch->branch_id, $name);
    }

    /*
     * @author: CSD
     * @description: create branch on business creation/setup
     * @return service_id
     */
    public static function createBranchService($branch_id, $business_name)
    {
        return Service::createService($branch_id, $business_name . " Service"); //ARA Moved function to createService
    }

    /*
     * @author ARA
     * @description get services based on business id
     * @
     */
    public static function getServicesByBusinessId($business_id)
    {
        return Service::join('branch', 'service.branch_id', '=', 'branch.branch_id')
            ->where('branch.business_id', '=', $business_id)
            ->select('service.service_id', 'service.branch_id', 'branch.business_id', 'service.name')
            ->get();
    }

    public static function getBusinessServicesWithTerminals($business_id)
    {
        $services = Service::getServicesByBusinessId($business_id);
        foreach ($services as $service) {
            $terminals = Terminal::getTerminalsByServiceId($service->service_id);
            $service->terminals = Terminal::getAssignedTerminalWithUsers($terminals);
        }
        return $services;
    }

    /*
     * @author: CSD
     * @description: fetch services by branch id
     * @return services array of branch
     */
    public static function getServicesByBranchId($branch_id)
    {
        return Service::where('branch_id', '=', $branch_id)->get();
    }

    public static function deleteServicesByBranchId($branch_id)
    {
        return Service::where('branch_id', '=', $branch_id)->delete();
    }

    public static function getFirstServiceOfBusiness($business_id)
    {
        $first_branch = Branch::getFirstBranchOfBusiness($business_id);
        if(is_null($first_branch)) {
            return null;
        }
        return Service::getFirstServiceOfBranch($first_branch->branch_id);
    }

    public static function getFirstServiceOfBranch($branch_id)
    {
        return Service::where('branch_id', '=', $branch_id)->first();
    }

    public static function updateServiceName($service_id, $name)
    {
        Service::where('service_id', '=', $service_id)->update(['name' => $name]);
    }

    public static function deleteService($service_id)
    {
        $terminals = Terminal::getTerminalsByServiceId($service_id);
        foreach ($terminals as $terminal) {
            Terminal::deleteTerminal($terminal['terminal_id']);
        }
        Service::where('service_id', '=', $service_id)->delete();
    }
}