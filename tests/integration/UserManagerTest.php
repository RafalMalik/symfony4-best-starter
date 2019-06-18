<?php

namespace App\Tests\Integration;

use App\Entity\User;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserManagerTest extends KernelTestCase
{

    /** @var UserManager $userManager */
    public $userManager;

    protected function setUp()
    {
        static::bootKernel();

        $this->userManager = self::$container->get(UserManager::class);
    }


    /**
     * @dataProvider CreateSuccessProvider
     * @param $userData
     */
    public function testCreateSuccess($userData)
    {
        $user = new User();
        $user->setEmail($userData['email'])
            ->setRoles($userData['roles'])
            ->setPlainPassword($userData['password']);

        $persistedUser = $this->userManager->create($user);

        /* Check that persisted user is User class instance */
        $this->assertInstanceOf(User::class, $persistedUser);

        /* Check that persisted userID is not null */
        $this->assertNotEquals(null, $persistedUser->getId());

        /* Check that user data is valid with $userData */
        $this->assertEquals($userData['email'], $persistedUser->getEmail());
    }

    public function createSuccessProvider()
    {
        yield [['email' => 'huj112@o2.pl', 'password' => '123456', 'roles' => ['ROLE_USER']]];
    }


    /**
     * @dataProvider createFailedProvider
     * @param $userData
     */
    public function testCreateFailed($userData)
    {
        $user = new User();
        $user->setEmail($userData['email'])
            ->setRoles($userData['roles'])
            ->setPlainPassword($userData['password']);

        $persistedUser = $this->userManager->create($user);

        /* Check that user data is valid with $userData */
        $this->assertEquals(null, $persistedUser);
    }

    public function createFailedProvider()
    {
        yield [['email' => 'huj112@o2.pl', 'password' => '123456', 'roles' => ['ROLE_USER']]];
    }
}
