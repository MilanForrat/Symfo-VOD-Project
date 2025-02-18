<?php

namespace App\Controller\Admin;

// on veut des éléments de l'entité Video donc on l'appelle
use App\Entity\Video;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Header;
use App\Entity\Language;
use App\Entity\Order;
use App\Entity\StatsVideo;
use App\Entity\StatsEvent;
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
            ->setTitle('Espace Administration')
            ->setTranslationDomain('fr');
    }


    // c'est ici qu'on ajoute des menus/liens accès sur la plateforme admin
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Page Accueil Visiteurs', 'fa-solid fa-house', 'app_home');
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Articles', 'fa-solid fa-newspaper', Article::class);
        yield MenuItem::linkToCrud('Vidéos', 'fa-solid fa-video', Video::class);
        yield MenuItem::linkToCrud('Evènements', 'fa-solid fa-list', Event::class);
        yield MenuItem::linkToCrud('Catégories', 'fa-solid fa-list', Category::class);
        yield MenuItem::linkToCrud('Langues', 'fa-solid fa-list', Language::class);
        yield MenuItem::linkToCrud('Commandes', 'fa-solid fa-list', Order::class);
        yield MenuItem::linkToCrud('Bannières', 'fa-solid fa-list', Header::class);
        yield MenuItem::linkToCrud('Statistiques Vidéos', 'fa-solid fa-list', StatsVideo::class);
        yield MenuItem::linkToCrud('Statistiques Evènements', 'fa-solid fa-list', StatsEvent::class);

    }

    // lien vers les icones : https://fontawesome.com/icons/
}
