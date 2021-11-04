<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AdminController extends AbstractController
{
//    #[Route('/admin', name: 'admin')]
//    public function index(): Response
//    {
//        return $this->render('admin/index.html.twig', [
//            'controller_name' => 'AdminController',
//        ]);
//    }

    #[Route('/admin/users', name: 'admin.users')]
    public function usersList(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/usersList.html.twig',[
            'users' =>$users
        ]);
    }

    #[Route('/admin/users/update/{id}', name: 'admin.user.update')]
    public function update(
        User $user,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
    )
    {
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password',
                RepeatedType::class,[
                    'type' => PasswordType::class,
//                    'required' => true,
                    'first_options' => ['label' => 'Create new Password'],
                    'second_options' => ['label' => 'Confirm new Password']
                ])
            ->add('update', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success float-right'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $updatedUser = $form->getData();
//    dump($form,  $data, $request);
//    die();

          //  $user->setEmail($data['email']);
//            $user->setPassword(
//                $passwordEncoder->encodePassword($user, $data['password'])
//            );

            $em = $this->getDoctrine()->getManager();
//            $em->persist($user);
            $em->persist($updatedUser);
            $em->flush();

            return $this->redirect($this->generateUrl('admin.users'));
        }


        return $this->render('admin/updateUser.html.twig',[
            'form' => $form->createView(), 'user' => $user
        ]);
    }

}
