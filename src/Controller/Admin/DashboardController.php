<?php

namespace App\Controller\Admin;

// on veut des éléments de l'entité Video donc on l'appelle
use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// va nous permettre de générer des URLS d'admin 
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // ici c'est la page par défaut
        // return parent::index();

        // on récupère le AdminUrlGenerator qui va permettre de créer une URL
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        // permet de créer l'url concernant le Video Crud Controller
        $url = $routeBuilder->setController(VideoCrudController::class)->generateUrl();
        // on demande une redirection ver l'url en question
        return $this->redirect($url);



        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    // cette fonction nous permet de configurer des éléments sur notre page admin
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('test');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::linkToCrud('Vidéos', 'fa-solid fa-video', Video::class);
    }

    // lien vers les icones : https://fontawesome.com/icons/
}
