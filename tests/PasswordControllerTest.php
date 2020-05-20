<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class PasswordControllerTest
 * @package App\Tests
 */
class PasswordControllerTest extends TestCase
{
    /**
     *
     */
    const API_BASE_URL = 'http://localhost';

    /**
     *
     */
    public function testPOST()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', self::API_BASE_URL . '/api/password/generate', [
            'form_params' => [
                'user_id' => 1
            ]
        ]);

        $this->assertEquals(200, $res->getStatusCode());

        $response = \GuzzleHttp\json_decode($res->getBody(), true);

        $this->assertArrayHasKey('password', $response);
    }

    /**
     *
     */
    public function testPUT()
    {
        $client = new \GuzzleHttp\Client();
        $resPassword = $client->request('POST', self::API_BASE_URL . '/api/password/generate', [
            'form_params' => [
                'user_id' => 1
            ]
        ]);

        $body = \GuzzleHttp\json_decode($resPassword->getBody());

        $resValidate = $client->request('PUT', self::API_BASE_URL . '/api/password/validate', [
            'form_params' => [
                'user_id' => 1,
                'password' => $body->password,
            ]
        ]);

        $this->assertEquals(200, $resValidate->getStatusCode());

        $response       = \GuzzleHttp\json_decode($resValidate->getBody(), true);

        $this->assertArrayHasKey('response', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertTrue($response['response']);

        sleep(130);

        $resValidate = $client->request('PUT', self::API_BASE_URL . '/api/password/validate', [
            'form_params' => [
                'user_id' => 1,
                'password' => $body->password,
            ]
        ]);

        $this->assertEquals(400, $resValidate->getStatusCode());

        $response       = \GuzzleHttp\json_decode($resValidate->getBody(), true);
        $this->assertFalse($response['response']);
    }
}
