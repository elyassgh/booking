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
                ],
                'data' => '5',
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
                    'Less than 3 Km' => 3,
                    'Less than 5 Km' => 5,
                    'Less than 10 Km' => 10,
                    'Less than 20 Km' => 20,
                ],
                'data' => '5',
            ])
            ->add('maxprice' , RangeType::class , [
                'attr' => [
                    "min" => 50,
                    "max" => 1000,
                    "step" => 10,
                    "value" => 250,
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
