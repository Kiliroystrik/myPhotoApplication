<?php

namespace App\Api\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagControllerTest extends WebTestCase
{
    public function testGetTagsSuccess(): void
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

        // Présence du token dans la réponse
        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'GET',
            '/api/tags',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $data['token'],
            ]
        );

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testGetTagsIsValid(): void
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

        // Présence du token dans la réponse
        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'GET',
            '/api/tags',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $data['token'],
            ]
        );

        $tag = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($tag);
    }

    public function testCreateTag(): void
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

        // Présence du token dans la réponse
        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'POST',
            '/api/tags',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $data['token'],
            ],
            json_encode([
                'name' => 'test',
            ])
        );
        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }

    public function testCreateTagFail(): void
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

        // Présence du token dans la réponse
        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'POST',
            '/api/tags',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $data['token'],
            ],
            json_encode([
                'name' => '',
            ])
        );

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }
}
