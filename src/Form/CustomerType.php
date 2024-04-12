<?php

namespace App\Form;

use App\Entity\Customer;
use Masterminds\HTML5;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name',
                    ]),
                ],
                'invalid_message' => 'The name fields must not be empty.',
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Age',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an age',
                    ]),
                    new Range([
                        'min' => 18,
                        'max' => 120,
                        'minMessage' => 'You must be at least {{ limit }} years old',
                        'maxMessage' => 'You cannot be older than {{ limit }} years old',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
