<?php

namespace App\Form;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PhotoAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Photo::class,
            'placeholder' => 'Recherche',
            // multiple choices labels with images inside an image balise
            'choice_label' => function (Photo $photo) {

                $title = $photo->getTitle();
                $imageUrl = $photo->getImageUrl();
                if ($imageUrl) {
                    $title = '<div style="display: flex; align-items: center;">
                                <img class="photo-card__image" style="width: 25px; height: 20px; margin-right: 5px;" src="/uploads/photos/mini/300x300-' . $imageUrl . '" alt="' . $photo->getTitle() . '" />
                                <p style="color: rgba(0, 0, 0, 0.6); margin: 0;">' . $title . '</p>
                              </div>';
                }

                return $title;
            },
            // 'choice_label' => 'title',

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['title', 'description', 'tags.name'],
            'query_builder' => function (PhotoRepository $photoRepository) {
                return $photoRepository->createQueryBuilder('title');
            },
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
