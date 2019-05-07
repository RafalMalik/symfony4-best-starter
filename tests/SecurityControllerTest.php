<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    public function testLogin()
    {
        $client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));

        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        //var_dump($client->getResponse()->getContent());

        $crawler = $client->submitForm('Sign in', [
            'email' => 'jano',
            'password' => 'my safe password',
        ]);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Email could not be found1.")')->count()
        );

        var_dump($client->getResponse()->getContent());
        //$buttonCrawlerNode = $crawler->selectButton('_submit');
        //$form = $buttonCrawlerNode->form();
//        $data = array('_username' => 'root','_password' => 'toor');
//        $client->submit($form,$data);
//
//        //here you're using $this->client not $client
//        $crawler = $this->client->followRedirect();
//        $crawler = $client->request('GET', '/crm/home');
    }

//    public function testSomething()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Hello World', $crawler->filter('h1')->text());
//    }
}
