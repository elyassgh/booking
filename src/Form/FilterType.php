<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('stars' , ChoiceType::class , [
                'choices'  => [
                    "1 Star" => 1,
                    "2 Stars" => 2,
                    "3 Stars" => 3,
                    "4 Stars" => 4,
                    "5 Stars" => 5,
                ]
            ])
            ->add('type' , ChoiceType::class , [
                'choices' => [
                    'Luxury Room' => 'Luxury room',
                    'Deluxe Room' => 'Deluxe room',
                    'Executive Room' => 'Executive room',
                    'Small Room' => 'Small room',
                ]
            ])
            ->add('distance' , ChoiceType::class , [
                'choices' => [
                    'City Center' => 0,
                    'Less than 1 Km from center' => 1,
                    'Less than 3 Km from center' => 3,
                    'Less than 5 Km from center' => 5,
                ]
            ])
            ->add('maxprice' , IntegerType::class , [
                'attr' => [
                    "data-provide" => "slider",
                    "data-slider-ticks" => "[150, 500, 750, 1000]",
                    "data-slider-ticks-labels" => '["150€", "500€", "750€", "1000€"]',
                    "data-slider-step" => "10",
                    "data-slider-value" => "500",
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'post',
            'csrf_protection' => 'false'
        ]);
    }
}
