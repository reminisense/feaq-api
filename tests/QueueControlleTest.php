<?php
/**
 * Created by PhpStorm.
 * User: JONAS
 * Date: 12/17/2015
 * Time: 2:12 PM
 */

class QueueControllerTest extends TestCase {

    public function testPostInsertSpecific(){

        $parameters = [

            'service_id' => 'bills',
            'terminal_id' => '',
            'queue_platform' => '',
            'priority_number' => '',
            'name' => '',
            'phone' => '',
            'email' => '',
            'date' => '',
            'user_id' => '',
            'time_assigned' => ''

        ];

        $response = $this->call('POST', '/queue/insert-specific', $parameters);

        $result = $response->getContent();

        $this->assertJson($result);

        $success = json_decode($result);

    }
}
