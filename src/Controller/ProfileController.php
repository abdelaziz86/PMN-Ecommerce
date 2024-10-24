<?php 

// src/Controller/ProfileController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Get the currently logged-in user
        /** @var User $user */
        $user = $this->getUser();

        // Create the form for editing user information
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle profile information update
            $entityManager->persist($user);

            // Handle password update
            $currentPassword = $form->get('currentPassword')->getData();
            if ($currentPassword) {
                // Check if the current password is valid
                if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
                    // Check if new password and confirm password match
                    $newPassword = $form->get('newPassword')->getData();
                    $confirmPassword = $form->get('confirmPassword')->getData();

                    if ($newPassword === $confirmPassword) {
                        // Encode the new password and set it
                        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                    } else {
                        // Add error message if passwords do not match
                        $this->addFlash('error', 'New password and confirmation do not match.');
                    }
                } else {
                    $this->addFlash('error', 'Current password is incorrect.');
                }
            }

            $entityManager->flush();  // Save changes

            // Add flash message for successful update
            $this->addFlash('success', 'Profile updated successfully!');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
