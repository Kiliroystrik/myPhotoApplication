<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Entity\Tag;
use App\Form\TagCrudType;
use App\Form\TagType;
use App\Repository\TagRepository;
use ContainerZtjHdBO\getTagCrudTypeService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PhotoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ImageField::new('imageUrl')
                ->setUploadDir('public/uploads/photos')
                ->setBasePath('uploads/photos')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('title'),
            TextField::new('slug'),
            NumberField::new('price'),
            AssociationField::new('tags')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),

        ];
    }
}
