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
     * @dataProvider successWewProvider
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
    public function successWewProvider()
    {
        yield [['email' => 'email@o2.pl', 'password' => '123456']];
        yield [['email' => 'esadmail@o2.pl', 'password' => '12asd3456']];
        yield [['email' => 'emkozakail@oasd2.pl', 'password' => '123asd456']];
    }

    /**
     * Test to user/show action
     */
    public function testShow()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/show');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/show")')->count()
        );

        /* Check that profile data are equals with stored user */
        $user = $this->em->getRepository(User::class)->find(3);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("' . $user->getEmail() . '")')->count()
        );
    }

    /**
     * Test to user/edit action
     */
    public function testEdit()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/edit');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/edit")')->count()
        );

        /* Fill form and create user */
        $crawler = $this->client->submitForm('Create', [
            'user[email]' => 'email@o2.pl'
        ]);

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("User has been edited")')->count()
        );

    }

    /**
     * Test to user/show action
     */
    public function testDelete()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/user/delete');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("user/delete")')->count()
        );
//        $crawler = $this->client->followRedirect();

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains flashbag info */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("User has been deleted")')->count()
        );
    }


}
