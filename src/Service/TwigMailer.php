<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TwigMailer
{

    /** @var Swift_Mailer $mailer */
    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * @param $subject
     * @param $to
     * @param $template
     * @param array $parameters
     * @return mixed
     */
    public function send($subject, $to, $template, array $parameters = [])
    {
        $message = (new Swift_Message($subject))
            ->setFrom('send@example.com')
            ->setTo($to)
            ->setBody(
                $this->renderView(
                    $template,
                    $parameters
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }


    /**
     * Send registration email after create user.
     *
     */
    public function registration(User $user) {
        $this->send(
            'User created',
            $user->getEmail(),
            'email/registration.html.twig'
        );
    }

    /**
     *
     */
    public function resetting(User $user) {
        $this->send(
            'Reset password',
            $user->getEmail(),
            'email/resetting.html.twig'
        );
    }



}