<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();

        // Set the default role for a simple user
        $user->setRoles(['ROLE_USER']); // Default to simple user role

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Check if the passwords match
            if ($plainPassword !== $confirmPassword) {
                // Add an error to the confirm password field
                $form->get('confirmPassword')->addError(new FormError('Passwords do not match.'));
            } else {
                // Set the user's properties
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );

                // Set name and surname from the form
                $user->setName($form->get('name')->getData());
                $user->setSurname($form->get('surname')->getData());

                // Persist the user entity
                $entityManager->persist($user);
                $entityManager->flush();

                // Add a flash message for success
                $this->addFlash('success', 'Registration successful! You can now log in.');

                // Redirect to the login page or any other page
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
