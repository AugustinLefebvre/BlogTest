<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{   
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserRepository $repo, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $repo->add($user, true);
            return $this->redirectToRoute('login');
        }
        return $this->renderForm('security/register.html.twig', array(
            'registerForm' => $form
        ));
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $address = $request->headers->get('referer');
        return $this->renderForm('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            'referer' => $address,
        ));
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {

    }
}
