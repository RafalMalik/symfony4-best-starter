<?php

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
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
     * Check that user successfully login with valid credentials.
     *
     * @dataProvider successLoginProvider
     * @param $credentials
     */
    public function testSuccessLogin($credentials)
    {
        /* Go to login page and check that status = 200 */
        $this->client->request('GET', '/login');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Fill login form  */
        $this->client->submitForm('Login', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        var_dump($this->client->getResponse()->getContent());

        /* Handle redirect after success login */
        $crawler = $this->client->followRedirect();

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Hello AppController")')->count()
        );
    }

    public function successLoginProvider()
    {
        yield [['email' => 'user1@test.pl', 'password' => '2345']];
        yield [['email' => 'user2@test.pl', 'password' => '2345']];
    }


    /**
     * Check that user cannot login with invalid credentials.
     *
     * @dataProvider failedLoginProvider
     */
    public function testFailedLogin($credentials)
    {
        /* Go to login page and check that status = 200 */
        $this->client->request('GET', '/login');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Fill form with invalid data */
        $this->client->submitForm('Login', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        /* Handle redirect after login */
        $crawler = $this->client->followRedirect();

        /* Check that page after redirect contains once login error */
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
        yield [['email' => 'huj@o2.pl', 'password' => '123456']];
        yield [['email' => 'dyzio@o2.pl', 'password' => '123456']];
        yield [['email' => 'huj@o2.pl', 'password' => '12345']];
    }

}
