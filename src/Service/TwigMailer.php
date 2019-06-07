<?php

namespace App\Service;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Templating\EngineInterface;


class TwigMailer
{

    /** @var Swift_Mailer $mailer */
    private $mailer;

    /** @var EngineInterface $templating */
    private $templating;

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
        $message = (new TemplatedEmail($subject))
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
     * @param User $user
     */
    public function registration(User $user) {
        $this->send(
            'User created',
            $user->getEmail(),
            'email/registration.html.twig'
        );
    }

    /**
     * Send resetting email after change request.
     * @param User $user
     */
    public function resetting(User $user) {
        $this->send(
            'Reset password',
            $user->getEmail(),
            'email/resetting.html.twig'
        );
    }



}