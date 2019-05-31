<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{

    /** @var Client $client */
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));
    }

    /**
     * Check that user successfully register with valid data.
     *
     * @dataProvider successRegisterProvider
     * @param $registration
     */
    public function testSuccessRegister($registration)
    {
        /* Go to register page and check that status = 200 */
        $this->client->request('GET', '/register');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Fill register form  */
        $this->client->submitForm('Register', [
            'registration_form[email]' => $registration['email'],
            'registration_form[plainPassword]' => $registration['password'],
        ]);

        /* Handle redirect after success login */
        $crawler = $this->client->followRedirect();

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Sign In")')->count()
        );
    }

    public function successRegisterProvider()
    {
        yield [['email' => 'neewcecadasdczz3m123zcxza3w3dail@1oa9.pl', 'password' => '123456']];
        yield [['email' => 'newweczxwazcmasdasd1123czxcwacail@o11c0.pl', 'password' => '123456']];
    }


}
