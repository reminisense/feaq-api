<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 12/17/2015
 * Time: 1:33 PM
 */

class LandingPageControllerTest extends TestCase {

    public function testSearchSuccess(){

        $parameters = [

            'keyword' => 'bills',
            'country' => '',
            'industry' => '',
            'time_open' => '',
            'timezone' => '',
            'limit' => '',
            'offset' => '',

        ];

        $response = $this->call('POST', '/business/search', $parameters);

        $result = $response->getContent();

        $this->assertJson($result);

        $success = json_decode($result);

        if($success){

            $business = $success[0];

            $this->assertObjectHasAttribute("business_id", $business);
            $this->assertInternalType("int", $business->business_id);

            $this->assertObjectHasAttribute("business_name", $business);
            $this->assertInternalType("string", $business->business_name);

            $this->assertObjectHasAttribute("local_address", $business);
            $this->assertInternalType("string", $business->local_address);

            $this->assertObjectHasAttribute("time_open", $business);
            $this->assertInternalType("string", $business->time_open);

            $this->assertObjectHasAttribute("waiting_time", $business);
            $this->assertInternalType("string", $business->waiting_time);

            $this->assertObjectHasAttribute("last_number_called", $business);
            $this->assertInternalType("string", $business->last_number_called);

            $this->assertObjectHasAttribute("next_available_number", $business);
            $this->assertInternalType("int", $business->next_available_number);

            $this->assertObjectHasAttribute("last_active", $business);
            $this->assertInternalType("int", $business->last_active);

            $this->assertObjectHasAttribute("card_bool",$business);
            $this->assertInternalType("boolean", $business->card_bool);
        }
    }

}
