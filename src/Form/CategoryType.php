<?php
// src/Form/CategoryType.php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Category Name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            // Make sure CSRF protection is enabled
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'category_item', // Use a unique token ID
        ]);
    }
}
