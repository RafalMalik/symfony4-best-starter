<?php

namespace App\Controller;

use App\Security\ResetPasswordManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="security")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


    /**
     * View where user fill email address and request change password email.
     *
     * @Route("/reset-request", name="app_reset_request")
     */
    public function resetRequest(Request $request, ResetPasswordManager $resetPasswordManager) {
        /**
         * @todo Make resetting password form
         */

        if ($request->isMethod('POST') && $request->request->get('email') ) {

            try {

                $resetPasswordManager->processRequest($request->request->get('email'));

            } catch (\Exception $e) {
                var_dump('nie ma takiego emaila');
            }



            var_dump('teraz bedzie logika odpowiedzialna za resetowanie hasla');
            exit();
        }

        return $this->render('security/reset_request.html.twig', [
            'error' => []
        ]);
    }


    /**
     * @Route("/resetting/{resettingToken}", name="app_resetting")
     * @todo Make resetting form, when user with token can change his password.
     */
    public function resetting(Request $request, $resettingToken, ResetPasswordManager $resetPasswordManager) {

        if (!$resetPasswordManager->isValidToken($resettingToken)) {
            var_dump('niepoprawny token');exit();
        }


        if ($request->isMethod('POST')) {

            try {
                $resetPasswordManager->changePassword($resettingToken, $request->request->get('password'));

                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                var_dump('COS NIE TAK KURWA');
                exit();
            }



        }


        var_dump($resettingToken);

        return $this->render('security/resetting.html.twig', [
            'error' => []
        ]);

    }



}
