<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rue',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Rue',
                ]
            ])
            ->add('city',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Ville',
                ]
            ])
            ->add('pays',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Pays',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
