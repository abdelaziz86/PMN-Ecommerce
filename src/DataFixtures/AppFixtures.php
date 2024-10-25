<?php
// php bin/console doctrine:fixtures:load

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Define category names
        $categoryNames = [
            'Electronics',
            'Clothing',
            'Home & Kitchen',
            'Books',
            'Sports & Outdoors'
        ];

        // Create categories and persist them
        $categories = [];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Define product names and images
        $productNames = [
            'Wireless Earbuds', 'Smartphone Case', 'Gaming Laptop', 'Bluetooth Speaker', 'Smart TV',
            'Running Shoes', 'Graphic T-Shirt', 'Winter Jacket', 'Jeans', 'Sneakers',
            'Coffee Maker', 'Blender', 'Vacuum Cleaner', 'Electric Kettle', 'Microwave Oven',
            'Thriller Novel', 'Self-Help Book', 'Cookbook', 'Children’s Storybook', 'Travel Guide'
        ];

        $imageFiles = ['2.png', '3.png', '4.png', '6.jpeg', '7.jpg', 'air.png', 'iphone.jpg'];

        // Create products, randomly assign categories, descriptions, prices, and images
        foreach ($productNames as $productName) {
            $product = new Product();
            $product->setName($productName);
            $product->setDescription($faker->sentence(10)); 
            $product->setPrice($faker->randomFloat(2, 10, 200)); 
            $product->setQuantity($faker->numberBetween(1, 100)); 
            $product->setImage($faker->randomElement($imageFiles)); // Randomly assign one of the provided images
            $product->setCategory($faker->randomElement($categories)); 

            $manager->persist($product);
        }

        // Persist all data to the database
        $manager->flush();
    }
}
