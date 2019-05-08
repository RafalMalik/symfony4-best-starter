<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{user}", name="profile")
     * @param User|null $user
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo Make view with user data.
     */
    public function index(User $user = null)
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user ?? $this->getUser()
        ]);
    }


    /**
     * @Route("/edit", name="profile")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo Make view with user data.
     */
    public function edit(User $user = null)
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user ?? $this->getUser()
        ]);
    }
}
