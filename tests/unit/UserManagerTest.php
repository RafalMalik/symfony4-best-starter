<?php

namespace App\Tests\Unit;

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
     * @dataProvider CreateUserProvider
     */
    public function testCreate()
    {
        $user = new User();
        $user->setEmail('testaccount123@test.pl')
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('123456');

        $this->userManager->create($user);

        $this->assertTrue(true);
    }

    public function createUserProvider()
    {
        yield [['email' => 'huj@o2.pl', 'password' => '123456']];
        yield [['email' => 'dyzio@o2.pl', 'password' => '123456']];
    }
}
