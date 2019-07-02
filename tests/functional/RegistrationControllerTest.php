<?php

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class RegistrationControllerTest extends WebTestCase
{

    /** @var Client $client */
    private $client = null;

    public function setUp()
    {
        $this->loadFixtures([
            AppFixtures::class
        ]);

        $this->client = $this->makeClient(false, array(
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

        $this->assertStatusCode(200, $this->client);

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
            $crawler->filter('h1:contains("Hello AppController!")')->count()
        );
    }


    public function successRegisterProvider()
    {
        yield [['email' => 'neewcecadasdczz3m123zcxza3w3dail@1oa9.pl', 'password' => '123456']];
        yield [['email' => 'newweczxwazcmasdasd1123czxcwacail@o11c0.pl', 'password' => '123456']];
    }



    /**
     * Check that user successfully register with valid data.
     *
     * @dataProvider failedRegisterProvider
     * @param $registration
     */
    public function testFailedRegister($registration)
    {
        /* Go to register page and check that status = 200 */
        $this->client->request('GET', '/register');

        $this->assertStatusCode(200, $this->client);

        /* Fill register form  */
        $this->client->submitForm('Register', [
            'registration_form[email]' => $registration['email'],
            'registration_form[plainPassword]' => $registration['password'],
        ]);

        $crawler = $this->client->getCrawler();

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("There is already an account with this email")')->count()
        );
    }


    public function failedRegisterProvider()
    {
        yield [['email' => 'user2@test.pl', 'password' => '123456']];
    }
}
