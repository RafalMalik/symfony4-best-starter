<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    public function testSuccessLogin()
    {
        $client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));

        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Sign in', [
            'email' => 'jano',
            'password' => 'my safe password',
        ]);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Email could not be found.")')->count()
        );
    }


    public function testFailedLogin()
    {
        $client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));

        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Sign in', [
            'email' => 'jano',
            'password' => 'my safe password',
        ]);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Email could not be found.")')->count()
        );


    }

}
