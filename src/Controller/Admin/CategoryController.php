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
use Symfony\Component\HttpFoundation\JsonResponse;

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
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/category/new", name="admin_category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category created successfully!');

            return $this->redirectToRoute('admin_categories'); // Redirect back to the category list
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/category/edit/{id}", name="admin_category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully!');
            return $this->redirectToRoute('admin_categories');
        }

        // Render the form on a separate page (no JSON, just a normal render)
        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }



    /**
     * @Route("/admin/category/delete/{id}", name="admin_category_delete", methods={"POST", "DELETE"})
     */
    public function delete(Category $category, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Check the CSRF token for security
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token, category deletion failed.');
        }

        return $this->redirectToRoute('admin_categories');
    }

}
