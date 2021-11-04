<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\CustomAuthentificatorAuthenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        CustomAuthentificatorAuthenticator $formAuthenticator
     ): Response
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();

            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

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

    //the controller is not used now
    #[Route('/register/success', name: 'register.success')]
    public function success():Response
    {
        return $this->render('admin/success.html.twig', [
        ]);
    }

}
