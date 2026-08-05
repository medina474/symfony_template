<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Tncc;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('country')
            ->add('long')
            ->add('flag')
            ->add('sepa')
            ->add('intracommunity')
            ->add('phone_code')
            ->add('tncc', EntityType::class, [
                'class' => Tncc::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
