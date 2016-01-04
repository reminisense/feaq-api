<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 12/17/2015
 * Time: 2:23 PM
 */

class UserControllerTest extends TestCase {

    public function testFetchProfileSuccess(){

        $user_id = 2;

        $response = $this->call('GET', '/user/'.$user_id);

        $result = $response->getContent();

        $this->assertJson($result);

        $user = json_decode($result);

        $this->assertObjectHasAttribute('user_id', $user);
        $this->assertInternalType('string', $user->user_id);

        $this->assertObjectHasAttribute('email', $user);
        $this->assertInternalType('string', $user->email);

        $this->assertObjectHasAttribute('first_name', $user);
        $this->assertInternalType('string', $user->first_name);

        $this->assertObjectHasAttribute('last_name', $user);
        $this->assertInternalType('string', $user->last_name);

        $this->assertObjectHasAttribute('phone', $user);
        $this->assertInternalType('string', $user->phone);

        $this->assertObjectHasAttribute('local_address', $user);
        $this->assertInternalType('string', $user->local_address);

    }

    public function testFetchProfileUserNotFound()
    {

        $user_id = 1231231231231232;

        $response = $this->call('GET', '/user/' . $user_id);

        $result = $response->getContent();

        $this->assertJson($result);

        $error = json_decode($result);

        $this->assertObjectHasAttribute('err_code', $error);
        $this->assertInternalType('string', $error->err_code);
        $this->assertEquals('UserNotFound', $error->err_code);

    }

    public function testUpdateUserSuccess()
    {

        $parameter = [

            'user_id' => '2',
            'first_name' => 'Awesome',
            'last_name' => 'common',
            'phone' => '09991415151',
            'local_address' => 'Sangi Lapu, Cebu'
        ];

        $response = $this->call('PUT', '/user/update',  $parameter);

        $result = $response->getContent();

        $this->assertJson($result);

        $success = json_decode($result);

        $this->assertObjectHasAttribute('success', $success);
        $this->assertInternalType('int', $success->success);
        $this->assertEquals(1, $success->success);

    }

    public function testUpdateUserUserNotFound()
    {

        $parameter = [

            'user_id' => '2123123213',
            'first_name' => 'Awesome',
            'last_name' => 'common',
            'phone' => '09991415151',
            'local_address' => 'Sangi Lapu, Cebu'
        ];

        $response = $this->call('PUT', '/user/update',  $parameter);

        $result = $response->getContent();

        $this->assertJson($result);

        $error = json_decode($result);

        $this->assertObjectHasAttribute('success', $error);
        $this->assertInternalType('int', $error->success);
        $this->assertEquals('0', $error->success);

        $this->assertObjectHasAttribute('err_code', $error);
        $this->assertInternalType('string', $error->err_code);
        $this->assertEquals('UserNotFound', $error->err_code);

    }

}
