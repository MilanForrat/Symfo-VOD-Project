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
        // permet de construire le slug à partir du name
        $slug=SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex();

        $title=TextField::new('title', "Titre");

        $subtitle = TextField::new('subtitle', "Sous-titre");

        $textContent = TextEditorField::new('textContent', "Contenu de l'article");

        $relation = AssociationField::new('category')->setSortProperty('name');

        $uploadedDate = DateField::new('uploadedDate', 'Date de mise en ligne')->setFormat('dd.MM.yyyy');

        $fields[]=$slug;
        $fields[]=$title;
        $fields[]=$subtitle;
        $fields[]=$textContent;
        $fields[]=$relation;
        $fields[]=$uploadedDate;

        return $fields;
    }
    
}
