<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfileControllerTest extends WebTestCase
{
    /** @var Client */
    private $client = null;

    /**
     * Initialize tests client with specific docker port
     */
    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'HTTP_HOST'       => 'localhost:1182',
        ));
    }

    public function logIn() {
        $session = $this->client->getContainer()->get('session');

        /**
         * @var EntityManagerInterface
         */
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');


        $user = $em->getRepository(User::class)->find(3);





        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context


        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $this->client->getContainer()->get('session')->set('_security_main', serialize($token));

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    /**
     * @todo All small init tests extract to method and execute after logIn.
     */
    public function basicTests() {

    }



    /**
     * Test profile/index page.
     */
    public function testProfile()
    {
        /* LogIn to application */

        /* Go to profile page and check status = 200 */

        /* Check that h1 contains "profile/index" */

        /* Search user email in h3 tag */

        /* Go to profile with parameter = 4 */

        /* Check status = 200 */

        /* Check h3 tag contains different email address */


        $this->logIn();
        /* Go to login page and check that status = 200 */
        $this->client->request('GET', '/profile');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    }

    /**
     * Test profile/edit page failed edit
     */

    public function testProfileFailedEdit()
    {
        /* LogIn to application */

        /* Go to profile page and check status = 200 */

        /* Check that h1 contains "profile/index" */

        /* Check that data in form are real and equals the log in user data */

        /* Change email value to invalid - phrase without @ (at) symbol */

        /* Send form and handle redirect */

        /* Find on results page that email field is invalid */

        /* Verify email address is the same as before send form */
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
