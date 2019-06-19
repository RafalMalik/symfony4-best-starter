<?php


namespace App\Tests\unit;


use App\Entity\User;
use App\Service\TwigMailer;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagerTest extends TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $encoder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenStorage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mailer;

    /**
     * @var UserManager
     */
    private $userManager;


    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $session = $this->createMock(SessionInterface::class);
        $mailer = $this->createMock(TwigMailer::class);

        $this->userManager = new UserManager(
            $this->em,
            $this->encoder,
            $tokenStorage,
            $session,
            $mailer
        );
    }


    public function testCreate()
    {
        $user = new User();
        $user->setPlainPassword('abcdefgh')
            ->setEmail('create@o2.pl');

        $this->encoder->expects($this->once())
            ->method('encodePassword')
            ->with($user, 'abcdefgh')
            ->will($this->returnValue('array'));

        $this->em->expects($this->once())
            ->method('persist')
            ->with(User::class);

        $this->userManager->create($user);

    }

}