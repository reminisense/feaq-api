<?php

namespace App\Http\Controllers;
use App\Models\AdImages;

class AdvertisementController extends Controller {

  /**
   * @api {get} advertisement/{business_id} Fetch All Image Ads of the Business
   * @apiName FetchAdvertisementImage
   * @apiGroup Advertisement
   * @apiVersion 1.0.0
   * @apiExample {js} Example Usage:
   *     https://api.featherq.com/advertisement/1
   * @apiDescription Gets all the image advertisements that have been uploaded by the business.
   *
   * @apiHeader {String} access-key The unique access key sent by the client.
   * @apiPermission Business Owner
   *
   * @apiParam {Number} business_id The id of the business.
   *
   * @apiSuccess (Success 200) {String} img_id The id of the image.
   * @apiSuccess (Success 200) {String} path The filesystem path of the image.
   * @apiSuccess (Success 200) {String} weight The weight/place of the image.
   * @apiSuccess (Success 200) {String} business_id The id of the business to which the image belongs.
   * @apiSuccessExample {Json} Success-Response:
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "img_id": 72,
   *         "path": "ads\/125\/o_1a2pute0r17ns1fi91p8q1vj6ric.jpg",
   *         "weight": 19,
   *         "business_id": 125
   *       },
   *       {
   *         "img_id": 74,
   *         "path": "ads\/125\/o_1a2pute0rmt3nm7f5o10927tue.png",
   *         "weight": 21,
   *         "business_id": 125
   *       }
   *     ]
   *
   * @apiError (Error 404) {String} NoImagesFound The <code>NoImagesFound</code> is null.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 404 Not Found
   *     {
   *       "err_message": "NoImagesFound"
   *     }
   */
  public function fetchImages($business_id = 0) {
    $ad_images = AdImages::fetchAllImagesByBusinessId($business_id);
    if ($ad_images) {
      return $ad_images;
    }
    else {
      return json_encode(array(
        'err_message' => 'NoImagesFound',
      ));
    }
  }

}