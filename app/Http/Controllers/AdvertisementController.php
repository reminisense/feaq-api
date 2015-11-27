<?php

namespace App\Http\Controllers;
use App\Models\AdImages;

class AdvertisementController extends Controller {

  /**
   * @api {get} advertisement/{business_id} Fetch all the Business Image Ads
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
   * @apiSuccess (200) {Object[]} ad_images The array of images found on the broadcast screen.
   * @apiSuccess (200) {Number} ad_images.img_id The id of the image.
   * @apiSuccess (200) {String} ad_images.path The filesystem path of the image.
   * @apiSuccess (200) {String} ad_images.weight The weight/place of the image.
   * @apiSuccess (200) {String} ad_images.business_id The id of the business to which the image belongs.
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
   * @apiError (200) {String} NoImagesFound No Images were found using the <code>business_id</code>.
   * @apiErrorExample {Json} Error-Response:
   *     HTTP/1.1 200 OK
   *     {
   *       "err_code": "NoImagesFound"
   *     }
   */
  public function getImages($business_id = 0) {
    if (AdImages::imageExistsByBusinessId($business_id)) {
      $ad_images = AdImages::fetchAllImagesByBusinessId($business_id);
      return $ad_images;
    }
    else {
      return json_encode(array(
        'err_code' => 'NoImagesFound'
      ));
    }
  }

}