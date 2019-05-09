<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function changePassword(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $passwordEncoder = $this->get('security.password_encoder');
            $oldPassword = $request->request->get('etiquettebundle_user')['oldPassword'];

            // Si l'ancien mot de passe est bon
            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($newEncodedPassword);

                $em->persist($user);
                $em->flush();

                $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                return $this->redirectToRoute('profile');
            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
            }
        }

        return $this->render('profile/change_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
