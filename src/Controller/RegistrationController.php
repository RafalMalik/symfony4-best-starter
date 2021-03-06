<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserManager $userManager
     * @return Response
     */
    public function register(Request $request, UserManager $userManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->create($user);

            $userManager->authenticate($user);

            return $this->redirectToRoute('app_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Redirect after success register to this page.
     *
     * @Route("/register/confirmation", name="app_register_confirmation")
     * @param Request $request
     */
    public function confirmation(Request $request)
    {
    }

    /**
     * Action when user confirm his account.
     *
     * @Route("/register/confirm/{token}", name="app_register_confirm")
     * @param Request $request
     * @param $token
     */
    public function confirm(Request $request, $token)
    {
    }
}
