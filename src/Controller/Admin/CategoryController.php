<?php
// src/Controller/CategoryController.php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Fetch all categories
        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Create the form for a new category
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        
        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category added successfully!');
            
            return $this->redirectToRoute('admin_categories'); // Redirect to the same page to see the new category
        }

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
            'form' => $form->createView(), // Pass the form to the view
        ]);
    }
}
