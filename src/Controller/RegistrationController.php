<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function register(Request $request, ValidatorInterface $validator, UserRoleRepository $userRoleRepository, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $user = new User();

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank()
                ]])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank()
            ]])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => ['label' => 'Password' ],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('register', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary block full-width m-b',
                    'style' => 'padding-bottom: 8px;'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $errors = $validator->validate($data);

            $checkIfRepeatEmailExits = $userRepository->findOneBy(
                array(
                    'email' => $data['email']
                )
            );

            if (count($errors) > 0 || $checkIfRepeatEmailExits !== null){
                $this->addFlash('warning', 'Please enter correct data');
                return $this->redirect($this->generateUrl('registration'));
            }
            else {

                $user->setEmail($data['email']);
                $user->setName($data['name']);
                $user->setLastname($data['lastname']);
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $data['password'])
                );
                $user->setUserrole($userRoleRepository->findOneBy(array('id' => 1)));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Registrated successfuly');
                return $this->redirect($this->generateUrl('app_login'));
            }
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
