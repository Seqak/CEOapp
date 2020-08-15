<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(UserRepository $userRepository)
    {
//        $user = $userRepository->findOneBy(array('email' => ))

        return $this->render('dashboard/index.html.twig', [
            'user'
        ]);
    }
}
