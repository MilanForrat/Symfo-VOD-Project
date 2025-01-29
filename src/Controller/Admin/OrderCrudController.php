<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // création d'une nouvelle action qui est reliée à la function show
        $show = Action::new('Afficher')->linkToCrudAction('show');

    return $actions
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ->add(Crud::PAGE_INDEX, $show)
    ;
    }

    public function show(AdminContext $context){
        $order = $context->getEntity()->getInstance();

        // dd($order);

        return $this->render('admin/order.html.twig',[
            'order'=>$order,
        ]);
    }

    public function configureCrud(Crud $crud): Crud{
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('Id'),
            DateField::new('createdAt')->setLabel('Date'),
            NumberField::new('status', 'Statut')->setTemplatePath('admin/status.html.twig'),
            AssociationField::new('user')->setLabel('Utilisateur/Client'),
            // récupère les getters dans l'entité Order
            NumberField::new('totalTVA')->setLabel('Montant de la TVA'),
            NumberField::new('totalHT')->setLabel('Total HT'),
            NumberField::new('totalTTC')->setLabel('Total TTC'),
        ];
    }

}
