<?php

namespace App\Api\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationControllerTest extends WebTestCase
{
    public function testAuthenticationAccessTokenSuccess(): void
    {
        $client = static::createClient();

        // Requête POST à l'endpoint de login
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'borges.mathieu@gmail.com',
                'password' => 'N4rutokado',
            ])
        );

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Présence du token dans la réponse
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        // validation non null du token
        $this->assertNotNull($data['token']);
    }

    public function testAuthenticationAccessTokenFail(): void
    {
        $client = static::createClient();

        // Requête POST à l'endpoint de login
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'invalid_username',
                'password' => 'invalid_password',
            ])
        );

        $this->assertSame(401, $client->getResponse()->getStatusCode());
    }
}
