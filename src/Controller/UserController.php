<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    /**
     * @todo Make action when admin can generate user,
     */
    public function new() {

    }

    /**
     * @todo Make show action, some like profile when shown all users data and link to edit profile.
     */
    public function show() {

    }

    /**
     * @todo Make edit action, when we have form with user data and can edit it.
     */
    public function edit() {

    }

    /**
     * @todo Make delete user action -> delete user with all related data.
     */
    public function delete() {

    }




}
