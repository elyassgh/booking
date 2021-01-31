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
                    "6 Stars" => 6,
                ]
            ])
            ->add('type' , ChoiceType::class , [
                'choices' => [
                    'Single room'=> 'Single room',
                    'King room' => 'King room',
                    'Luxury room' => 'Luxury room',
                    'Deluxe room' => 'Deluxe room',
                    'Twin room' => 'Twin room',
                    'Studio' => 'Studio'
                ]
            ])
            ->add('distance' , ChoiceType::class , [
                'choices' => [
                    'City Center' => 0,
                    'Less than 3 Km from center' => 3,
                    'Less than 5 Km from center' => 5,
                    'Less than 10 Km from center' => 10,
                    'Less than 20 Km from center' => 20,
                ]
            ])
            ->add('maxprice' , IntegerType::class , [
                'attr' => [
                    "data-provide" => "slider",
                    "data-slider-ticks" => "[50, 250, 500, 1000]",
                    "data-slider-ticks-labels" => '["50€", "250€", "500€", "1000€"]',
                    "data-slider-step" => "50",
                    "data-slider-value" => "250",
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
