<?php


namespace App\Tests\unit;


use App\Entity\User;
use App\Service\TwigMailer;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagerTest extends TestCase
{



    private $userManager;

    protected function setUp()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $session = $this->createMock(SessionInterface::class);
        $mailer = $this->createMock(TwigMailer::class);

        $this->userManager = new UserManager($em, $encoder, $tokenStorage, $session, $mailer);
    }


    public function testCreate()
    {

        $this->userManager = $this->createMock(UserManager::class);

        $this->userManager->expects($this->once())
            ->method('create')
            ->willReturn( array());
//
//        $user = new User();
//        $user->setPlainPassword('12345')
//            ->setEmail('create@o2.pl');
//
//        $this->userManager->create($user);



    }

}