<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserManager
{

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    /** @var SessionInterface $session */
    private $session;

    /** @var TwigMailer $mailer */
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        TwigMailer $twigMailer
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->mailer = $twigMailer;
    }


    /**
     * Prepare user before persist, encode password.
     *
     * @param User $user
     * @return User|null
     */
    public function create(User $user)
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $user->getPlainPassword()
            )
        );

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Here send confirmation email
            $this->mailer->registration($user);

            return $user;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $exception) {

            return null;
        }
    }

    /**
     * Update user in database.
     *
     * @param User $user
     */
    public function update(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }


    /**
     * Authenticate in firewall as $user.
     *
     * @param User $user
     * @param string $firewall
     */
    public function authenticate(User $user, $firewall = 'main')
    {
        /** @var UsernamePasswordToken $token */
        $token = $this->createToken($user, $firewall);

        $this->tokenStorage->setToken($token);

        $this->session->set('_security_main', serialize($token));
    }

    /**
     * Return authentication $user token in $firewall.
     *
     * @param User $user
     * @param $firewall
     * @return UsernamePasswordToken
     */
    public function createToken(User $user, $firewall)
    {
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());

        return $token;
    }

    /**
     * Search user by criteria array [key => value]
     *
     * @param array $criteria
     * @return User|null
     */
    public function getUser(array $criteria)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy($criteria);

        return $user;
    }
}