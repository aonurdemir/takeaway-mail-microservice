<?php

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testRegisterEndpointGetStatus201()
    {
        $response = $this->post('/api/v1/users');

        $response->assertStatus(201);
    }
}