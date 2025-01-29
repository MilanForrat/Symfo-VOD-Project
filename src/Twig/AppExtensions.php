<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

// création d'un filtre pour le formatage des prix affichés par twig
class AppExtensions extends AbstractExtension implements GlobalsInterface{

    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository){
        $this->categoryRepository = $categoryRepository;
    }

    public function getFilters(){
        return [
            new TwigFilter('price', [$this, 'formatPrice'])
        ];
    }



    public function formatPrice($number){
        return number_format($number,'2', ',').' €';
    }

    // va nous permettre de créer des variables globales dans twig (notamment utile pour la navbar qui contient des variables)
    public function getGlobals(): array{
        return[
            'allCategories'=> $this->categoryRepository->findAll(),
        ];
    }
}