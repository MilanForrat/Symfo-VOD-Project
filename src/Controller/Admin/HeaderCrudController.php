<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Bannière')
            ->setEntityLabelInPlural('Bannières');
    }
    
    public function configureFields(string $pageName): iterable
    {
        $required = true;
        if($pageName=="edit"){
            $required=false;
        }

        return [
            TextField::new('title', "Titre"),
            TextareaField::new('content', "Contenu"),
            TextField::new('buttonTitle', "Texte du bouton"),
            TextField::new('buttonLink', "Lien du bouton"),
            ImageField::new('image', 'Image de fond de la bannière')
                // répertoire dans lequel seront enregistrées les images
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setLabel('Image')
                ->setHelp("L'image ne doit pas dépasser 1200 Kb, soit 1,2 Mb")
                // on renomme l'image sur le serveur pour éviter le duplicata (nom unique à chaque image)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                // rendre obligatoire l'upload d'image
                ->setRequired($required)
        ];
    }
}
