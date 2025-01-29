<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class VideoCrudController extends AbstractCrudController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        ->setTranslationDomain('fr');
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Vidéo')
            ->setEntityLabelInPlural('Vidéos');
    }

    public function configureFields(string $pageName): iterable
    {
        $title=TextField::new('name', "Titre");

        // permet de construire le slug à partir du name
        $slug=SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex();

        $subtitle = TextField::new('subtitle', "Titre");

        $description = TextEditorField::new('description', "Description");

        $trailer = TextField::new('trailer', "Lien bande d'annonce");

        $link = TextField::new('link', "Lien vidéo complète");

        $language = AssociationField::new('language', 'Langue');

        $fields = [
            // personnalisation du système d'upload d'image
            // "image" est le nom du champ de l'entité et "Image" est le nom qui sera affiché dans easyAdmin
            ImageField::new('image', 'Image')
                // répertoire dans lequel seront enregistrées les images
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                // on renomme l'image sur le serveur pour éviter le duplicata (nom unique à chaque image)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                // rendre obligatoire l'upload d'image
                ->setRequired(false),
        ];

        
        $uploadedDate = DateField::new('uploadedDate', 'Date de mise en ligne')->setFormat('dd.MM.yyyy');

        // enlever le AM / PM sur le dashboard
        $length = TextField::new('length', 'Durée vidéo');

        $price = TextField::new('price', "Prix HT");

        $tva = ChoiceField::new('tva', "Taux de TVA")->setChoices([
            '5,5%'=>'5.5',
            '10%'=>'10',
            '20%'=>'20',
        ]);

        $relation = AssociationField::new('category', 'Catégorie')->setSortProperty('name');

        $fields[]=$slug;
        $fields[]=$title;
        $fields[]=$subtitle;
        $fields[]=$description;
        $fields[]=$trailer;
        $fields[]=$link;
        $fields[]=$relation;
        $fields[]=$language;
        $fields[]=$length;
        $fields[]=$price;
        $fields[]=$tva;
        $fields[]=$uploadedDate;

        return $fields;
    }
}
