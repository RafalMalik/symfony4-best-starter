<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        return $this->render('user/index.html.twig', [
            'pagination' => $paginator->paginate(
                $userRepository->findAll(),
                $request->query->getInt('page', 1),     10)
        ]);
    }


    /**
     * @Route("/new", name="app_user_new")
     *
     * @todo Make action when admin can generate user,
     */
    public function new(Request $request, UserManager $userManager)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userManager->create($user);

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{user}", name="app_user_show")
     *
     * @param User $user
     * @return Response
     * @todo Make show action, some like profile when shown all users data and link to edit profile.
     */
    public function show(User $user)
    {
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);

    }

    /**
     * @Route("/edit/{user}", name="app_user_edit")
     *
     * @param Request $request
     * @param User $user
     * @param UserManager $userManager
     * @return RedirectResponse|Response
     * @todo Make edit action, when we have form with user data and can edit it.
     */
    public function edit(Request $request, User $user, UserManager $userManager)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userManager->update($user);

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{user}", name="app_user_delete")
     *
     * @param Request $request
     * @param User $user
     * @param UserManager $userManager
     * @return Response
     * @todo Make delete user action -> delete user with all related data.
     */
    public function delete(Request $request, User $user, UserManager $userManager)
    {
        return $this->render('user/delete.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

}
