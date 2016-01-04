<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 1/4/2016
 * Time: 10:04 AM
 */

class AdminControllerTest extends TestCase {

    public function testGetBusinessNumbersSuccess(){

        $start_date = 1451836800; //Jan 4
        $end_date = 1451836800; //Jan 4

        $response = $this->call('GET', '/admin/stats/'.$start_date.'/'.$end_date);

        $result = $response->getContent();

        $this->assertJson($result);

        $business_numbers = json_decode($result);

        $this->assertObjectHasAttribute("success" , $business_numbers);
        $this->assertInternalType("int", $business_numbers->success);
        $this->assertEquals("1", $business_numbers->success);

        $this->assertObjectHasAttribute("businesses_count" , $business_numbers);
        $this->assertInternalType("int", $business_numbers->businesses_count);

        $this->assertObjectHasAttribute("businesses_information" , $business_numbers);
        if($business_numbers->businesses_information){

            $count = count( (array) $business_numbers->businesses_information);
            $business_information = $business_numbers->businesses_information;

            if($count > 1){

                $this->assertObjectHasAttribute("business_name", $business_information);
                $this->assertInternalType("string", $business_information->busines_name);

                $this->assertObjectHasAttribute("name", $business_information);
                $this->assertInternalType("string", $business_information->name);

                $this->assertObjectHasAttribute("email", $business_information);
                $this->assertInternalType("string", $business_information->email);

                $this->assertObjectHasAttribute("phone", $business_information);
                $this->assertInternalType("string", $business_information->phone);

            }else{

                $this->assertObjectHasAttribute("business_name", $business_information);
                $this->assertInternalType("string", $business_information->busines_name);
            }
        }

        $this->assertObjectHasAttribute("users_count" , $business_numbers);
        $this->assertInternalType("int", $business_numbers->users_count);

        $this->assertObjectHasAttribute("users_information" , $business_numbers);
        if($business_numbers->users_information){

            $users_information = $business_numbers->users_information;

            $this->assertObjectHasAttribute("first_name", $users_information);
            $this->assertInternalType("string", $users_information->first_name);

            $this->assertObjectHasAttribute("last_name", $users_information);
            $this->assertInternalType("string", $users_information->last_name);

            $this->assertObjectHasAttribute("email", $users_information);
            $this->assertInternalType("string", $users_information->email);

            $this->assertObjectHasAttribute("phone", $users_information);
            $this->assertInternalType("string", $users_information->phone);

        }

        $this->assertObjectHasAttribute("business_numbers", $business_numbers);
        $this->assertNotEmpty($business_numbers->business_numbers);

        $numbers = $business_numbers->business_numbers;

        $this->assertObjectHasAttribute("issued_numbers" , $numbers);
        $this->assertInternalType("int", $numbers->issued_numbers);

        $this->assertObjectHasAttribute("called_numbers" , $numbers);
        $this->assertInternalType("int", $numbers->called_numbers);

        $this->assertObjectHasAttribute("served_numbers" , $numbers);
        $this->assertInternalType("int", $numbers->served_numbers);

        $this->assertObjectHasAttribute("dropped_numbers" , $numbers);
        $this->assertInternalType("int", $numbers->dropped_numbers);
    }

}
