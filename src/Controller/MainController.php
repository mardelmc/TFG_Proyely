<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class MainController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/info', name: 'info')]
    final public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'menu')]
    final public function menu (): Response
    {
        return $this->render('main/menu.html.twig');
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    final public function admin (): Response
    {
        return $this->render('main/menuUsers.html.twig');
    }

    #[Route(path: '/signin', name: 'security_signin')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastAlias = $authenticationUtils->getLastUsername();
        return $this->render('main/login.html.twig', [
                'last_alias' => $lastAlias,
                'error' => $error
    ]);
    }

    #[Route(path: '/signout', name: 'security_signout')]
    public function logout(): void
    {
        throw new \LogicException('UwU');
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/user/profile', name: 'profile')]
    public function profile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'La contraseña se ha cambiado correctamente.');

            return $this->redirectToRoute('profile');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Hubo errores al cambiar la contraseña. Por favor, revisa los campos.');
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'changePasswordForm' => $form->createView(),
        ]);
    }
}