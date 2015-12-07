<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 12/4/2015
 * Time: 11:28 AM
 */

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Service;
use Illuminate\Support\Facades\Input;

class ServiceController extends Controller{
    /**
     * @api {post} /services Create Service
     * @apiName Create Service
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/services
     * @apiDescription Creates a new service for a business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} business_id The <code>business_id</code> of the business where the created service is under.
     * @apiParam {String} name The name of the service to be created.
     *
     * @apiSuccess (200) {Number} service_id Returns the <code>service_id</code> of the newly created service.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "service_id": 1
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the service creation fails.
     * @apiError (Error) {String} error_message Gives the reason for the service creation failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Business does not exist."
     *      }
     *
     */
    public function postCreateService(){
        if(Business::where('business_id', '=', Input::get('business_id'))->exists()){
            $service_id = Service::createBusinessService(Input::get('business_id'), Input::get('name'));
            return json_encode(['service_id' => $service_id]);
        }else{
            return json_encode(['success' => 0, 'error_message' => 'Business does not exist.']);
        }
    }

    /**
     * @api {put} /services/{service_id} Update Service
     * @apiName Update Service
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/services/1
     * @apiDescription Updates the name of a service
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {String} name The name of the service to be created.
     *
     * @apiSuccess (200) {Number} success Returns <code>1</code> if the update is successful.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the service update fails.
     * @apiError (Error) {String} error_message Gives the reason for the service update failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Service does not exist"
     *      }
     *
     */
    public function putUpdateService($service_id){
        if(Service::where('service_id', '=', $service_id)->exists()){
            Service::updateServiceName($service_id, Input::get('name'));
            return json_encode(['success' => 1]);
        }else{
            return json_encode(['success' => 0, 'error_message' => 'Service does not exist.']);
        }
    }

    /**
     * @api {delete} /services/{service_id} Delete Service
     * @apiName Delete Service
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/services/1
     * @apiDescription Deletes a specific service
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiSuccess (200) {Number} success Returns <code>1</code> if the update is successful.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the service deletion fails.
     * @apiError (Error) {String} error_message Gives the reason for the service deletion failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "error_message": "Service does not exist"
     *      }
     *
     */
    public function deleteRemoveService($service_id){
        if(Service::where('service_id', '=', $service_id)->exists()){
            Service::deleteService($service_id);
            return json_encode(['success' => 1]);
        }else{
            return json_encode(['success' => 0, 'error_message' => 'Service does not exist.']);
        }
    }
}