<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 12/16/2015
 * Time: 11:42 AM
 */

class BroadcastControllerTest extends TestCase {

    public function testGetImagesSuccess(){

        $raw_code  = 'f300'; //Pre-defined

        $response = $this->call('GET', '/broadcast/'.$raw_code);

        $result = $response->getContent();

        $this->assertJson($result);

        $success = json_decode($result);

        $this->assertObjectHasAttribute("business_id" , $success);
        $this->assertInternalType('int', $success->business_id);

        $this->assertObjectHasAttribute("adspace_size" , $success);
        $this->assertInternalType('string', $success->adspace_size);

        $this->assertObjectHasAttribute("numspace_size" , $success);
        $this->assertInternalType('string', $success->numspace_size);

        $this->assertObjectHasAttribute("carousel_delay" , $success);
        $this->assertInternalType('int', $success->carousel_delay);

        $this->assertObjectHasAttribute("ad_type" , $success);
        $this->assertInternalType('string', $success->ad_type);

        $this->assertObjectHasAttribute("ad_images" , $success);
        if($success->ad_images){

            $image = $success->ad_images[0];

            $this->assertObjectHasAttribute('img_id', $image);
            $this->assertInternalType('int', $image->img_id);

            $this->assertObjectHasAttribute('path', $image);
            $this->assertInternalType('string', $image->path);

            $this->assertObjectHasAttribute('weight', $image);
            $this->assertInternalType('int', $image->weight);

            $this->assertObjectHasAttribute('business_id', $image);
            $this->assertInternalType('int', $image->business_id);
        }

        $this->assertObjectHasAttribute('box_num' , $success);
        $this->assertInternalType('string', $success->box_num);

        $this->assertObjectHasAttribute('get_num' , $success);
        $this->assertInternalType('int', $success->get_num);

        $this->assertObjectHasAttribute('display' , $success);
        $this->assertInternalType('string', $success->display);

        $this->assertObjectHasAttribute('show_issued' , $success);
        $this->assertInternalType('string', $success->show_issued);

        $this->assertObjectHasAttribute('ad_video' , $success);
        $this->assertInternalType('string', $success->ad_video);

        $this->assertObjectHasAttribute('turn_on_tv' , $success);
        $this->assertInternalType('string', $success->turn_on_tv);

        $this->assertObjectHasAttribute('tv_channel' , $success);
        $this->assertInternalType('string', $success->tv_channel);

        $this->assertObjectHasAttribute('date' , $success);
        $this->assertInternalType('string', $success->date);

        $this->assertObjectHasAttribute('ticker_message' , $success);
        $this->assertInternalType('string', $success->ticker_message);

        $this->assertObjectHasAttribute('ticker_message2' , $success);
        $this->assertInternalType('string', $success->ticker_message2);

        $this->assertObjectHasAttribute('ticker_message3' , $success);
        $this->assertInternalType('string', $success->ticker_message3);

        $this->assertObjectHasAttribute('ticker_message4' , $success);
        $this->assertInternalType('string', $success->ticker_message4);

        $this->assertObjectHasAttribute('ticker_message5' , $success);
        $this->assertInternalType('string', $success->ticker_message5);

        $this->assertObjectHasAttribute('open_hour' , $success);
        $this->assertInternalType('int', $success->open_hour);

        $this->assertObjectHasAttribute('open_minute' , $success);
        $this->assertInternalType('int', $success->open_minute);

        $this->assertObjectHasAttribute('open_ampm' , $success);
        $this->assertInternalType('string', $success->open_ampm);

        $this->assertObjectHasAttribute('close_hour' , $success);
        $this->assertInternalType('int', $success->close_hour);

        $this->assertObjectHasAttribute('close_minute' , $success);
        $this->assertInternalType('int', $success->close_minute);

        $this->assertObjectHasAttribute('close_ampm' , $success);
        $this->assertInternalType('string', $success->show_issued);

        $this->assertObjectHasAttribute('local_address' , $success);
        $this->assertInternalType('string', $success->local_address);

        $this->assertObjectHasAttribute('business_name' , $success);
        $this->assertInternalType('string', $success->business_name);

        $this->assertObjectHasAttribute('first_service' , $success);

        $service = $success->first_service;

        $this->assertObjectHasAttribute('service_id' , $service);
        $this->assertInternalType('int', $service->service_id);

        $this->assertObjectHasAttribute('code' , $service);
        $this->assertInternalType('string', $service->code);

        $this->assertObjectHasAttribute('name' , $service);
        $this->assertInternalType('string', $service->name);

        $this->assertObjectHasAttribute('status' , $service);
        $this->assertInternalType('int', $service->status);

        $this->assertObjectHasAttribute('time_created' , $service);
        $this->assertInternalType('string', $service->time_created);

        $this->assertObjectHasAttribute('branch_id' , $service);
        $this->assertInternalType('int', $service->branch_id);

        $this->assertObjectHasAttribute('repeat_type' , $service);
        $this->assertInternalType('string', $service->repeat_type);

        $this->assertObjectHasAttribute('keywords' , $success);
        if($success->keywords){

            $keyword = $success->keywords[0];

            $this->assertInternalType('string', $keyword);

        }
    }

}
