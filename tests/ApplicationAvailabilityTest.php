<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityTest extends WebTestCase
{

    /**
     * Check that pages are public available.
     *
     * @dataProvider successUrlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function successUrlProvider()
    {
        yield ['/login'];
        yield ['/register'];
        yield ['/reset-request'];
    }

    /**
     * Check that pages are public available.
     *
     * @dataProvider secureUrlProvider
     */
    public function testPageIsSecure($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }


    public function secureUrlProvider()
    {
        yield ['/'];
    }
}
