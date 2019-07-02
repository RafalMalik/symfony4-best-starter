<?php

namespace App\Tests\Functional;


use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    /** @var Client */
    private $client = null;

    /** @var User $user  */
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
    }

    public function logIn() {
        $session = $this->client->getContainer()->get('session');

        $this->user = $this->em->getRepository(User::class)->findOneBy([]);

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
     * Test profile/index page.
     */
    public function testProfile()
    {
        /* LogIn to application */
        $this->logIn();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/profile');

        /* Check that response status is HTTP::OK */
        $this->assertStatusCode(200, $this->client);

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("profile/index")')->count()
        );

        /* Search user email in h3 tag */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("' . $this->user->getEmail() . '")')->count()
        );

        /* Check h3 tag contains different email address */
        $user = $this->em->getRepository(User::class)->findBy(array(), array(), 1, 1)[0];

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/profile/' . $user->getId());

        /* Check status = 200 */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check h3 tag contains different email address */
        $this->assertEquals(
            0,
            $crawler->filter('html:contains("' . $this->user->getEmail() . '")')->count()
        );

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("' . $user->getEmail() . '")')->count()
        );
    }

    /**
     * Test profile/edit page failed edit
     */

    public function testProfileFailedEdit()
    {
        $this->markTestSkipped('SKIP');



        /* LogIn to application */
        $this->logIn();

        /* Store user email after send form */
        $email = $this->user->getEmail();

        /* Go to profile page and check status = 200 */
        $crawler = $this->client->request('GET', '/profile/edit');

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        /* Check that page after redirect contains search phrase */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("profile/edit")')->count()
        );

        /* Check that data in form are real and equals the log in user data */
        $this->assertEquals(
            $this->user->getEmail(),
            $crawler->filter('#profile_email')->attr('value')
        );

        /* Change email value to invalid - phrase without @ (at) symbol */
        /* Fill login form  */
        $crawler = $this->client->submitForm('Submit', [
            'profile[email]' => 'invalid_email_format2'
        ]);

        /* Handle redirect after save profile form */
        //$crawler = $this->client->followRedirect();

        /* Check that response status is HTTP::OK */
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            -1,
            $crawler->filter('html:contains("invalid form data")')->count()
        );

        /* Verify email address is the same as before send form */
        $this->assertEquals(
            $this->user->getEmail(),
            $crawler->filter('#profile_email')->attr('value')
        );
    }

    /**
     * Test profile/edit page success edit
     */

    public function testProfileSuccessEdit()
    {
        /* LogIn to application */

        /* Go to profile page and check status = 200 */

        /* Check that h1 contains "profile/edit" */

        /* Check that data in form are real and equals the log in user data */

        /* Change email value to valid - add '1' symbol before email address */

        /* Send form and handle redirect */

        /* Check that we are on profile/index page */

        /* Check status */

        /* Test email address is equals with user */
    }


    /**
     * Test profile/change-password page failed edit
     * @todo Create this test.
     */

    public function testProfileFailedChangePassword()
    {
        /* LogIn to application */

        /* Go to profile page and check status = 200 */

        /* Check that h1 contains "profile/change-password" */

        /* Check that data in form are real and equals the log in user data */

        /* Change email value to invalid - phrase without @ (at) symbol */

        /* Send form and handle redirect */

        /* Find on results page that email field is invalid */

        /* Verify email address is the same as before send form */
    }



}
