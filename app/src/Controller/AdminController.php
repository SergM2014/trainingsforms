<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AdminController extends AbstractController
{

    #[Route('/admin/users', name: 'admin.users')]
    public function listUsers(UserRepository $userRepository):Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/usersList.html.twig',[
            'users' =>$users
        ]);
    }

    #[Route('/admin/users/update/{id}', name: 'admin.user.update')]
    public function updateUser(
        User $user,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
    ):Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $updatedUser = $form->getData();
            $updatedUser->setPassword($passwordEncoder->encodePassword($updatedUser, $updatedUser->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'User was created');

            return $this->redirect($this->generateUrl('admin.users'));
        }

//        return $this->render('admin/updateUser.html.twig',[
//            'form' => $form->createView(), 'user' => $user
//        ]);
        return $this->renderForm('admin/updateUser.html.twig', [ 'form' => $form, 'user' => $user ] );
    }

}
