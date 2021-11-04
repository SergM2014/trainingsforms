<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'admin.users')]
    public function usersList(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/usersList.html.twig',[
            'users' =>$users
        ]);
    }

    #[Route('/admin/users/update/{id}', name: 'admin.user.update')]
    public function update(User $user)
    {


        return $this->render('admin/usersList.html.twig',[
            'user' =>$user
        ]);
    }

}
