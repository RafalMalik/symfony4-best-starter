<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{user}", name="app_profile")
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
     * @Route("/edit", name="app_profile_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo Make view with user data.
     */
    public function edit(User $user = null)
    {
        return $this->render('profile/edit.html.twig', [
            'user' => $user ?? $this->getUser()
        ]);
    }

    /**
     * @Route("/change-password", name="app_profile_change_password")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo Make change password form and view.
     */
    public function changePassword(User $user = null)
    {
        return $this->render('profile/change_password.html.twig', [
            'user' => $user ?? $this->getUser()
        ]);
    }
}
