<?php

namespace App\Form;

use App\Entity\Photo;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('title')
            ->add('imageUrl')
            // ->add('metaInfo', TextareaType::class, [
            //     'attr' => ['rows' => 5], // Pour ajuster la hauteur du champ textarea
            //     // Ajoutez d'autres options si nécessaire, comme des contraintes de validation
            // ])
            ->add('price')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('modifiedAt', null, [
                'widget' => 'single_text'
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
