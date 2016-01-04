<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 12/15/2015
 * Time: 4:08 PM
 */

class AuthenticationControllerTest extends TestCase {

    public function testLoginSuccess(){

        $parameters = [
            'fb_id' => '10204727398937668',
            'click_source' => ''
        ];

        $response = $this->call('POST', '/login', $parameters);

        $result = $response->getContent();

        $this->assertJson($result);

        $success = json_decode($result);

        $this->assertObjectHasAttribute("success" , $success);
        $this->assertInternalType("int", $success->success);
        $this->assertEquals("1", $success->success);

        $this->assertObjectHasAttribute("accessToken" , $success);
        $this->assertInternalType("string", $success->accessToken);
    }


    public function testLoginMissingValue(){

        $parameters = [
            'fb_id' => '',
            'click_source' => ''
        ];

        $response = $this->call('POST', '/login', $parameters);

        $result = $response->getContent();

        $this->assertJson($result);

        $error = json_decode($result);

        $this->assertObjectHasAttribute("err_code", $error);
        $this->assertInternalType("string", $error->err_code);
        $this->assertEquals("MissingValue", $error->err_code);
    }

    public function testLoginSignUpRequired(){

        $parameters = [
            'fb_id' => '123123asdasdasd',
            'click_source' => ''
        ];

        $response = $this->call('POST', '/login', $parameters);

        $result = $response->getContent();

        $this->assertJson($result);

        $error = json_decode($result);

        $this->assertObjectHasAttribute("err_code", $error);
        $this->assertInternalType("string", $error->err_code);
        $this->assertEquals("SignUpRequired", $error->err_code);
    }

    public function testLogoutSuccess(){

        $response = $this->call('GET', '/logout');

        $result = $response->getContent();

        $this->assertJson($result);

        $success = json_decode($result);

        $this->assertObjectHasAttribute("success" , $success);
        $this->assertInternalType("int", $success->success);
        $this->assertEquals("1", $success->success);
    }

//    public function testRegisterSuccess(){
//
//
//    }

}

