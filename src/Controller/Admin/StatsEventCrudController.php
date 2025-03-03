<?php

namespace App\Controller\Admin;

use App\Entity\StatsEvent;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StatsEventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StatsEvent::class;
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
            ->setEntityLabelInSingular('Statistiques Evènements')
            ->setEntityLabelInPlural('Statistiques Evènements');
    }

    public function configureActions(Actions $actions): Actions
    {

        return $actions
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ->remove(Crud::PAGE_DETAIL, Action::DELETE)
    ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'Id')->hideOnIndex(),
            IntegerField::new('event_id','ID de l\'évènement'),
            TextField::new('event_name', "Evènement"),
            IntegerField::new('noFoodStats','Formule Pass\'Event Seul'),
            IntegerField::new('withFoodStats','Formule Pass\'Event + Repas'),
            IntegerField::new('play_count','Nombre total d\'achats'),
        ];
    }
}
