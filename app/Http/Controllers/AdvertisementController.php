<?php

namespace App\Http\Controllers;
use App\Models\AdImages;

class AdvertisementController extends Controller {

  public function getImages($business_id = 0) {
    $ad_images = AdImages::getAllImagesByBusinessId($business_id);
    return json_encode(array(
      'ad_images' => $ad_images,
    ));
  }

}