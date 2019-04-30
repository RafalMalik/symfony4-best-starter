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
        $crawler = $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), 'CHUJ');

        $buttonCrawlerNode = $crawler->selectButton('_submit');
        $form = $buttonCrawlerNode->form();
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
