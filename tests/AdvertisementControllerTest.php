<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 12/3/2015
 * Time: 12:37 PM
 */

class AdvertisementControllerTest extends TestCase {

    public function testGetImagesSuccess(){

        $business_id = 144; //Pre-defined

        $response = $this->call('GET', '/advertisement/'.$business_id);

        $result = $response->getContent();

        $this->assertJson($result);

        $array_of_images = json_decode($result);

        $this->assertNotEmpty($array_of_images);

        $image = $array_of_images[0];

        $this->assertObjectHasAttribute("img_id" , $image);
        $this->assertInternalType("int", $image->img_id);

        $this->assertObjectHasAttribute("path" , $image);
        $this->assertInternalType("string", $image->path);

        $this->assertObjectHasAttribute("weight" , $image);
        $this->assertInternalType("int", $image->weight);

        $this->assertObjectHasAttribute("business_id" , $image);
        $this->assertInternalType("int", $image->business_id);
    }

    public function testGetImagesNoImagesFound(){

        $business_id = 144123123; //Pre-defined

        $response = $this->call('GET', '/advertisement/'.$business_id);

        $result = $response->getContent();

        $this->assertJson($result);

        $error = json_decode($result);

        $this->assertObjectHasAttribute("err_code" , $error);
        $this->assertInternalType("string", $error->err_code);
        $this->assertEquals("NoImagesFound", $error->err_code);
    }

}


