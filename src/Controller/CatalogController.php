<?php
// src/Controller/CatalogController.php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends AbstractController
{
    /**
     * @Route("/", name="catalog")
     */
    public function catalog(Request $request, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Product::class);

        // Initialize variables for filtering, searching, and sorting
        $selectedCategoryId = $request->query->get('category'); // Get selected category ID
        $search = $request->query->get('search'); // Get search term
        $sort = $request->query->get('sort'); // Get sort option

        // Build the query with filters and sorting
        $query = $repository->createQueryBuilder('p');

        if ($search) {
            $query->andWhere('p.name LIKE :search OR p.description LIKE :search')
                  ->setParameter('search', '%' . $search . '%');
        }

        if ($selectedCategoryId) {
            $query->andWhere('p.category = :category')
                  ->setParameter('category', $selectedCategoryId);
        }

        if ($sort) {
            if ($sort === 'name') {
                $query->orderBy('p.name', 'ASC');
            } elseif ($sort === 'price') {
                $query->orderBy('p.price', 'ASC');
            }
        }

        $products = $query->getQuery()->getResult();

        // Fetch categories for filter dropdown
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('catalog/index.html.twig', [
            'products' => $products,
            'search' => $search,
            'category' => $selectedCategoryId,
            'sort' => $sort,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId, 
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_detail")
     */
    public function productDetail($id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('catalog/product_detail.html.twig', [
            'product' => $product,
        ]);
    }
}
