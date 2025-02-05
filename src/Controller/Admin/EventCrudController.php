<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        ->setTranslationDomain('fr');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Evènement')
            ->setEntityLabelInPlural('Evènements');
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;
        if($pageName=="edit"){
            $required=false;
        }

        return [
            IdField::new('id')->onlyOnIndex(),
            ImageField::new('image', 'Image')
                // répertoire dans lequel seront enregistrées les images
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setLabel('Image')
                ->setHelp("L'image ne doit pas dépasser 1200 Kb, soit 1,2 Mb")
                // on renomme l'image sur le serveur pour éviter le duplicata (nom unique à chaque image)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                // rendre obligatoire l'upload d'image
                ->setRequired($required),
            TextField::new('name', "Titre"),
            TextField::new('subtitle', "Sous-titre"),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex()->setHelp('URL de votre évènement générée automatiquement'),
            TextEditorField::new('description', "Description")->hideOnIndex(),
            TextField::new('eventPriceNoFood', "Prix TTC évènement sans repas")->hideOnIndex()->setHelp('Généralement 10.40'),
            TextField::new('eventPriceWithFood', "Prix TTC évènement + repas")->hideOnIndex()->setHelp('Généralement 26.40'),       
            DateTimeField::new('reservationDateEnd', 'Fin des réservations')->setFormat('dd.MM.yyyy'),
            DateTimeField::new('eventDate', 'Date évènement')->setFormat('dd.MM.yyyy'),
            BooleanField::new('isHomepage', "A là une ?")->setHelp('Evènement mis à la une sur la page d\'accueil'),
        ];  
    }
    
}
