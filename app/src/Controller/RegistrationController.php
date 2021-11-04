<?php

namespace App\Controller;

use App\Entity\User;
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

        $form = $this->createFormBuilder()
            ->add('email')
            ->add('password',
                RepeatedType::class,[
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password']
                ])
            ->add('register', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success float-right'
                ]
            ])
            ->getForm()
        ;
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $data = $form->getData();
//dump($data);
//die();
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //воно якогось хрена не робило I mean не було автоматичної привязки користувача при регістрації
           //return $this->redirect($this->generateUrl('register.success'));

            return $authenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request);
        }

        return $this->render('admin/index.html.twig',[
            'form' => $form->createView()
        ] );

    }

    //the controller is not used now
    #[Route('/register/success', name: 'register.success')]
    public function success():Response
    {
        return $this->render('admin/success.html.twig', [
        ]);
    }

}
