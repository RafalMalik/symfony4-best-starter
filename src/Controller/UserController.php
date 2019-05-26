<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository)
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }


    /**
     * @Route("/new", name="app_user_new")
     *
     * @todo Make action when admin can generate user,
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show", name="app_user_show")
     *
     * @todo Make show action, some like profile when shown all users data and link to edit profile.
     */
    public function show() {
        return $this->render('user/show.html.twig', [
            'controller_name' => 'UserController',
        ]);

    }

    /**
     * @Route("/edit", name="app_user_edit")
     *
     * @todo Make edit action, when we have form with user data and can edit it.
     */
    public function edit() {

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/delete", name="app_user_delete")
     *
     * @todo Make delete user action -> delete user with all related data.
     */
    public function delete() {
        return $this->render('user/delete.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

}
