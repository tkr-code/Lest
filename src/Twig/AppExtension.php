<?php

namespace App\Twig;

use App\Repository\CommentRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Article;
use App\Service\Cart\CartService;

class AppExtension extends AbstractExtension
{
    private $commentRepository;
    private $cartService;
    public function __construct(CartService $cartService, CommentRepository $commentRepository)
    {
        $this->cartService = $cartService;
        $this->commentRepository = $commentRepository;
    }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('date_format_fr', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cartTotal', [$this, 'cartTotal']),
            new TwigFunction('cartAll', [$this, 'cartAll']),
            new TwigFunction('rating', [$this, 'rating']),
            new TwigFunction('dateFilter', [$this, 'doSomething']),
        ];
    }

    public function cartTotal(){
        return $this->cartService->getTotal();
    }

    public function cartAll(){
        return $this->cartService->getFullCart();
    }

    public function rating(Article $article){
       return $this->commentRepository->rating($article);
    }

    public function doSomething($date)
    {
        //   $date = new \DateTime($date);
        switch ($date->format('N')) {
            case '01':
                // code...
                $jour = 'lundi';
                break;
            case '02':
                // code...
                $jour = 'mardi';
                break;
            case '03':
                // code...
                $jour = 'mercredi';
                break;
            case '04':
                // code...
                $jour = 'jeurdi';
                break;
            case '05':
                // code...
                $jour = 'vendredi';
                break;
            case '06':
                // code...
                $jour = 'samedi';
                break;
            case '07':
                // code...
                $jour = 'dimanche';
                break;

            default:
                // code...
                return 'Entrer le jour';
                break;
        }
        switch ($date->format('n')) {
            case '1':
                // code...
                $mois = 'Janvier';
                break;
            case '2':
                // code...
                $mois = 'février';
                break;
            case '3':
                // code...
                $mois = 'mars';
                break;
            case '4':
                // code...
                $mois = 'avril';
                break;
            case '5':
                // code...
                $mois = 'mai';
                break;
            case '6':
                // code...
                $mois = 'juin';
                break;
            case '7':
                // code...
                $mois = 'juillet';
                break;
            case '8':
                // code...
                $mois = 'août';
                break;
            case '9':
                // code...
                $mois = 'septembre';
                break;
            case '10':
                // code...
                $mois = 'octobre';
                break;
            case '11':
                // code...
                $mois = 'novembre';
                break;
            case '12':
                // code...
                $mois = 'décembre';
                break;

            default:
                // code...
                break;
        }

        return $jour.' '.$date->format('d').' '.$mois.' '.$date->format('Y');
    }
}
