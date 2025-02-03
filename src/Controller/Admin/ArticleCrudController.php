<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;


use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles');
    }
    
    public function configureFields(string $pageName): iterable
    {
        $required = true;
        if($pageName=="edit"){
            $required=false;
        }

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
                ->setRequired($required),
        ];
        // permet de construire le slug à partir du name
        $slug=SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex()->setHelp('URL de votre article généré automatiquement');

        $relation = AssociationField::new('category', 'Catégorie')->setSortProperty('name');

        $title=TextField::new('title', "Titre");

        $subtitle = TextField::new('subtitle', "Sous-titre");

        $textContent = TextEditorField::new('textContent', "Contenu de l'article")->hideOnIndex();

        $isHomepage = BooleanField::new('isHomepage', "A là une ?")->setHelp('Vidéo mise à la une sur la page d\'accueil');

        $uploadedDate = DateField::new('uploadedDate', 'Date de mise en ligne')->setFormat('dd.MM.yyyy');

        
        $fields[]=$relation;
        $fields[]=$title;
        $fields[]=$slug;
        $fields[]=$subtitle;
        $fields[]=$textContent;
        $fields[]=$uploadedDate;
        $fields[]=$isHomepage;

        return $fields;
    }
    
}
