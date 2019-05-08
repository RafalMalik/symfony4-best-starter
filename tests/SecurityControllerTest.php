<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    /**
     * Check that user successfully login with valid credentials.
     *
     * @dataProvider successLoginProvider
     * @param $credentials
     */
    public function testSuccessLogin($credentials)
    {
        $client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));

        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Sign in', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Hello AppController")')->count()
        );
    }


    public function successLoginProvider()
    {
        yield [['email' => 'huj@o2.pl', 'password' => '123456']];
        yield [['email' => 'dyzio@o2.pl', 'password' => '123456']];
    }


    /**
     * Check that user cannot login with invalid credentials.
     *
     * @dataProvider failedLoginProvider
     */
    public function testFailedLogin($credentials)
    {
        $client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));

        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Sign in', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Email could not be found.")')->count() +
            $crawler->filter('html:contains("Invalid credentials.")')->count()
        );
    }

    public function failedLoginProvider()
    {
        yield [['email' => 'huj1@o2.pl', 'password' => '123456']];
        yield [['email' => 'huj2@o2.pl', 'password' => '123456']];
        yield [['email' => 'huj3@o2.pl', 'password' => '123456']];
        yield [['email' => 'huj4@o2.pl', 'password' => '123456']];
        yield [['email' => 'huj5@o2.pl', 'password' => '123456']];
        yield [['email' => 'huj@o2.pl', 'password' => '12345']];
    }

}
