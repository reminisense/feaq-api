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
     * @apiName CreateService
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
     * @apiError (Error) {String} err_code Gives the reason for the service creation failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "NoBusinessFound"
     *      }
     *
     */
    public function postCreateService(){
        return Service::createBusinessService(Input::get('business_id'), Input::get('name'));
    }

    /**
     * @api {put} /services/{service_id} Update Service
     * @apiName UpdateService
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/services/1
     * @apiDescription Updates the name of a service
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} service_id The id of the service to be updated.
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
     * @apiError (Error) {String} err_code Gives the reason for the service update failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "NoServiceFound"
     *      }
     *
     */
    public function putUpdateService($service_id){
        return Service::updateServiceName($service_id, Input::get('name'));
    }

    /**
     * @api {delete} /services/{service_id} Delete Service
     * @apiName DeleteService
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage
     *      http://api.featherq.com/services/1
     * @apiDescription Deletes a specific service
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} service_id The id of the service to be updated.
     *
     * @apiSuccess (200) {Number} success Returns <code>1</code> if the update is successful.
     * @apiSuccessExample {Json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 1,
     *      }
     *
     * @apiError (Error) {Number} success Returns <code>0</code> if the service deletion fails.
     * @apiError (Error) {String} err_code Gives the reason for the service deletion failure.
     * @apiErrorExample {Json} Error-response:
     *      HTTP/1.1 200 OK
     *      {
     *          "success": 0,
     *          "err_code": "NoServiceFound"
     *      }
     *
     */
    public function deleteRemoveService($service_id){
        return Service::deleteService($service_id);
    }
}