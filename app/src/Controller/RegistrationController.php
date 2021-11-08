<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\CustomAuthentificatorAuthenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function index(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserAuthenticatorInterface $authenticator,
        CustomAuthentificatorAuthenticator $formAuthenticator,
        UserRepository $userRepository
     ): Response
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $user = $form->getData();
            $password = $form->get('password')->getData();

            $userRepository->save($user, $password, $passwordEncoder);

            $this->addFlash('success', 'User was created');
            return $authenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request);
        }

        return $this->renderForm('admin/index.html.twig', [
            'form' => $form,
        ]);

    }


}
