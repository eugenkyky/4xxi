<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('symbol', TextType::class, array(
                'attr' => array('maxlength' => 10),
                'required' => true,
            ))
            ->add('amount', IntegerType::class, array(
                'attr' => array('maxlength' => 10000000, 'min' => 1),
                'required' => true,
            ))
            ->add('save', SubmitType::class);
    }
}