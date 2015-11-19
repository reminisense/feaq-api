<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\AdImages;
use App\Models\Service;

class BroadcastController extends Controller
{

  public function getDetails($raw_code = '') {
    $business_id = Business::getBusinessIdByRawCode($raw_code);
    //$data = json_decode(file_get_contents(public_path() . '/json/' . $business_id . '.json'));
    $business_name = Business::name($business_id);
    $open_hour = Business::openHour($business_id);
    $open_minute = Business::openMinute($business_id);
    $open_ampm = Business::openAMPM($business_id);
    $close_hour = Business::closeHour($business_id);
    $close_minute = Business::closeMinute($business_id);
    $close_ampm = Business::closeAMPM($business_id);
    $ad_images = AdImages::getAllImagesByBusinessId($business_id);
    $first_service = Service::getFirstServiceOfBusiness($business_id);
    //$allow_remote = QueueSettings::allowRemote($first_service->service_id);
    $local_address = Business::localAddress($business_id);
    //$lines_in_queue = Analytics::getBusinessRemainingCount($business_id);
    //$estimate_serving_time = Analytics::getAverageTimeServedByBusinessId($business_id, 'string', $date, $date);
    $keywords = Business::getKeywordsByBusinessId($business_id);
    return json_encode(array(
      'business_id' => $business_id,
      //'adspace_size' => $data->adspace_size,
      //'carousel_delay' => $data->carousel_delay ? (int)$data->carousel_delay : 5000,
      //'ad_type' => $data->ad_type,
      'ad_images' => $ad_images,
      //'box_num' => explode("-", $data->display)[1], // the second index tells how many numbers to show in the broadcast screen
      'open_hour' => $open_hour,
      'open_minute' => $open_minute,
      'open_ampm' => $open_ampm,
      'close_hour' => $close_hour,
      'close_minute' => $close_minute,
      'close_ampm' => $close_ampm,
      'local_address' => $local_address,
      'business_name' => $business_name,
      //'lines_in_queue' => $lines_in_queue,
      //'estimate_serving_time' => $estimate_serving_time,
      'first_service' => $first_service,
      //'allow_remote' => $allow_remote,
      'keywords' => $keywords,
    ));
  }

}