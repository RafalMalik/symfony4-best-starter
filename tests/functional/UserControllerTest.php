<?php

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{

    /** @var Client */
    private $client = null;

    /** @var User $user */
    private $user = null;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Initialize tests client with specific docker port
     */
    public function setUp()
    {
        $this->loadFixtures([
            AppFixtures::class
        ]);

        $this->client = $this->makeClient(false, array(
            'HTTP_HOST'       => 'localhost:1182',
        ));

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $this->user = $this->em->getRepository(User::class)->find(3);
    }

    public function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';

        $token = new UsernamePasswordToken($this->user, null, $firewallName, $this->user->getRoles());
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $this->client->getContainer()->get('session')->set('_security_main', serialize($token));

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/');

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("app/index")')->count()
        );
    }


    /**
     * Test to user/index action
     */
    public function testIndex()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/index")')->count()
        );

        /* Check that user table row are equal summary number of users */
        $users = $this->em->getRepository(User::class)->findAll();

        /* Check pagination element */
        $this->assertGreaterThanOrEqual(
            count($users) / 10,
            $crawler->filter('.pagination .current, .pagination .page')->count()
        );

        /* Check that page has exactly 10 element */
        $this->assertEquals(
            10,
            $crawler->filter('.table-row')->count()
        );

        /* Click button "NEW" and redirect to new page */
        $btnNew  = $crawler->filter('.app-btn-new')->eq(0)->link();
        $crawler = $this->client->click($btnNew);

        /* Check that page contains text user/new */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/new")')->count()
        );
    }

    /**
     * @dataProvider successNewProvider
     */
    public function testSuccessNew($data)
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/new');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/new")')->count()
        );

        /* Fill form and create user */
        $this->client->submitForm('Create', [
            'user[email]' => $data['email'],
            'user[plainPassword]' => $data['password']
        ]);

        /* Check that we are redirect to user list with flash message */
        $crawler = $this->client->followRedirect();

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("User has been added.")')->count()
        );
    }

    /**
     * Data provider for testNew
     */
    public function successNewProvider()
    {
        yield [['email' => 'email@o2.pl', 'password' => '123456']];
        yield [['email' => 'esadmail@o2.pl', 'password' => '12asd3456']];
        yield [['email' => 'emkozakail@oasd2.pl', 'password' => '123asd456']];
    }

    /**
     * @dataProvider failedNewProvider
     */
    public function testFailedNew($data)
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/new');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/new")')->count()
        );

        /* Fill form and create user */
        $crawler = $this->client->submitForm('Create', [
            'user[email]' => $data['email'],
            'user[plainPassword]' => $data['password']
        ]);

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/new")')->count()
        );

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("The email is not a valid email")')->count() +
            $crawler->filter('html:contains("Your password should be at least 6 characters")')->count()
        );
    }

    /**
     * Data provider for test failed new
     */
    public function failedNewProvider()
    {
        yield [['email' => 'email.pl', 'password' => '123456']];
        yield [['email' => 'esadmail@o2.pl', 'password' => '12']];
        yield [['email' => 'emkozakail', 'password' => '']];
    }


    /**
     * Test to user/show action
     */
    public function testShow()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/show/' . $this->user->getId());

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/show")')->count()
        );

        /* Check that profile data are equals with stored user */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("' . $this->user->getEmail() . '")')->count()
        );
    }

    /**
     * @dataProvider successEditProvider
     * Test to user/edit action
     */
    public function testSuccessEdit($data)
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/edit/' . $this->user->getId());

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/edit")')->count()
        );

        /* Fill form and create user */
        $this->client->submitForm('Create', [
            'user[email]' => $data['email'],
            'user[plainPassword]' => $data['password']
        ]);

        $crawler = $this->client->followRedirect();

            /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/show")')->count()
        );

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("User has been edited")')->count()
        );

        /* Check that profile data are equals with stored user */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("' . $data['email'] . '")')->count()
        );
    }

    /**
     * Data provider for test success edit
     */
    public function successEditProvider()
    {
        yield [['email' => 'email@03.pl', 'password' => '123456']];
        yield [['email' => 'email@03.pl', 'password' => '1234568']];
        yield [['email' => 'email@03.pl', 'password' => '12fdsfdsf']];
    }


    /**
     * @dataProvider failedEditProvider
     * Test to user/edit failed action
     */
    public function testFailedEdit($data)
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/edit/' . $this->user->getId());

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/edit")')->count()
        );

        /* Fill form and create user */
        $crawler = $this->client->submitForm('Create', [
            'user[email]' => $data['email'],
            'user[plainPassword]' => $data['password']
        ]);

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/edit")')->count()
        );

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("The email is not a valid email")')->count() +
            $crawler->filter('html:contains("Please enter a password")')->count() +
            $crawler->filter('html:contains("Your password should be at least 6 characters")')->count()
        );
    }

    /**
     * Data provider for test success edit
     */
    public function failedEditProvider()
    {
        yield [['email' => 'email03.pl', 'password' => '12345']];
        yield [['email' => 'email@03.pl', 'password' => '']];
        yield [['email' => 'email@03.pl', 'password' => '12fd']];
    }


    /**
     * Test to user/show action
     */
    public function testDelete()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/index")')->count()
        );

        /* Click button "Delete" and redirect to index page with flashbag */
        $btnDelete  = $crawler->filter('.table-row .btn-danger')->eq(0)->link();
        $crawler = $this->client->click($btnDelete);

        $crawler = $this->client->followRedirect();

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page contains text user/new */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/index")')->count()
        );

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("User has been deleted")')->count()
        );
    }


}
