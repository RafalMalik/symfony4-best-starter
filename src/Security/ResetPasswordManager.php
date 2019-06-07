<?php

namespace App\Security;

use App\Entity\User;
use App\Service\TwigMailer;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ResetPasswordManager
{
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $mailer;
    private $userManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TwigMailer $twigMailer,
        UserManager $userManager
    )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $twigMailer;
        $this->userManager = $userManager;
    }


    /**
     * Process logic of resetting password, check users, change token and send email.
     *
     * @param string $email
     * @throws \Exception
     */
    public function processRequest(string $email)
    {
        $user = $this->userManager->getUser(['email' => $email]);

        if (!$user) {
            throw new \Exception('NIE MA TAKIEGO EMAIL W BAZIE');
        }

        /** Change user resetting token */
        $user->setResettingToken($this->getToken());

        /* Update user after change resetting token */
        $this->userManager->update($user);

        /** Send email contains resetting link with token */
        $this->mailer->resetting($user);
            }


    /**
     * Simple version with uniqid.
     */
    private function getToken()
    {
        return uniqid();
    }

    /**
     * Check that $token is assign to user then return true, otherwise return false.
     * @param string $resettingToken
     * @return bool
     */
    public function isValidToken(string $resettingToken): bool
    {
        if (!$resettingToken) {
            return false;
        }

        /** @var User $user */
        $user = $this->userManager->getUser(['resettingToken' => $resettingToken]);

        if (!$user) {
            return false;
        }

        return true;
    }


    /**
     * Change password in database.
     *
     * @param $resettingToken
     * @param $plainPassword
     */
    public function changePassword($resettingToken, $plainPassword)
    {
        $user = $this->userManager->getUser(['resettingToken' => $resettingToken]);

        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword))
            ->setResettingToken(null);

        $this->userManager->update($user);
    }
}
