<?php
// php bin/console doctrine:fixtures:load
namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categoryNames = [
            'Electronics',
            'Clothing',
            'Home & Kitchen',
            'Books',
            'Sports & Outdoors'
        ];

        $categories = [];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($faker->sentence(10));
            $manager->persist($category);
            $categories[] = $category; // Store each category for later use with products
        }

        $productNames = [
            'Wireless Earbuds', 'Smartphone Case', 'Gaming Laptop', 'Bluetooth Speaker', 'Smart TV',
            'Running Shoes', 'Graphic T-Shirt', 'Winter Jacket', 'Jeans', 'Sneakers',
            'Coffee Maker', 'Blender', 'Vacuum Cleaner', 'Electric Kettle', 'Microwave Oven',
            'Thriller Novel', 'Self-Help Book', 'Cookbook', 'Children’s Storybook', 'Travel Guide'
        ];

        $productImages = ['2.png', '3.png', '4.png', '6.jpeg', '7.jpg', 'air.png', 'iphone.jpg'];

        foreach ($productNames as $productName) {
            $product = new Product();
            $product->setName($productName);
            $product->setDescription($faker->sentence(10));
            $product->setPrice($faker->randomFloat(2, 10, 200));
            $product->setQuantity($faker->numberBetween(1, 100));
            $product->setImage($faker->randomElement($productImages)); // Assign a random image from the list
            $product->setCategory($faker->randomElement($categories)); // Assign a random category

            $manager->persist($product);
        }

        // Step 3: Create Users
        $usersData = [
            [
                'email' => 'admin@gmail.com',
                'roles' => ['ROLE_ADMIN'],
                'plainPassword' => 'azerty',
                'name' => 'Alice',
                'surname' => 'Smith'
            ],
            [
                'email' => 'user@gmail.com',
                'roles' => ['ROLE_USER'],
                'plainPassword' => 'azerty',
                'name' => 'John',
                'surname' => 'Doe'
            ]
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setName($userData['name']);
            $user->setSurname($userData['surname']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['plainPassword']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
