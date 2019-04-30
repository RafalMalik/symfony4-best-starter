<?php

namespace App\Security;

use App\Entity\User;
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

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Process logic of resetting password, check users, change token and send email.
     *
     * @param string $email
     */
    public function processRequest(string $email)
    {

        $user = $this->getUser($email);

        if (!$user) {
            throw new \Exception('NIE MA TAKIEGO EMAIL W BAZIE');
        }

        /** Change user resetting token */
        $user->getResettingToken($this->getToken());

        /** Send email contains resetting link with token */
        $this->sendResettingEmail($user);

    }

    public function sendResettingEmail(User $user)
    {

        $user->setResettingToken($this->getToken());

        var_dump('wysylam niby do typa maila ' . $user->getResettingToken());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser(string $email)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => $email]
        );

        if (!$user) {
            return false;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_index'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
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

        $user = $this->getUserByToken($resettingToken);

        if (!$user) {
            return false;
        }

        return true;
    }


    private function getUserByToken(string $resettingToken)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['resettingToken' => $resettingToken]
        );

        return $user;
    }

    public function changePassword($resettingToken, $plainPassword)
    {
        $user = $this->getUserByToken($resettingToken);

        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        var_dump('zmieniono password dla jakiegos typa');

    }
}
