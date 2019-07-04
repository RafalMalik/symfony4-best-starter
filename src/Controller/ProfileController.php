<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/change-password", name="app_profile_change_password")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo Make change password form and view.
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();

            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $newEncodedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
                $user->setPassword($newEncodedPassword);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                return $this->redirectToRoute('app_profile');
            } else {
                $form->addError(new FormError('Invalid old password'));
            }
        }

        return $this->render('profile/change_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/profile/edit", name="app_profile_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo Make view with user data.
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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
}
