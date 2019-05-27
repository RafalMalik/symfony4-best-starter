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

    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordEncoderInterface $passwordEncoder,
                                TokenStorageInterface $tokenStorage,
                                SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
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
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $exception) {
            //$this->update($user);
        }

        return $user;
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
    public function createToken(User $user, $firewall) {
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());

        return $token;
    }


}