<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function register(Request $request)
    {


        return $this->render('registration/index.html.twig', [
            'form' => "Temporary deleted"
        ]);
    }
}
