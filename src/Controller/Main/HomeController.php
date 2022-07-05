<?php

namespace App\Controller\Main;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ArticleSearch;
use App\Form\ArticleSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ClientRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/",  name="home")
     */
    public function home(Request $request, ArticleRepository $articleRepository): Response
    {
        $search = new ArticleSearch();
        $form = $this->createForm(ArticleSearchType::class,$search)->handleRequest($request);
        dump(
            $articleRepository->findCategoryTitle('imprimante et accessoires')
        );
      return  $this->renderForm("lest/home/index.html.twig", [
            'form'=>$form,
            'articles'=>[
                'ordinateurs'=>$articleRepository->findCategoryTitle('ordinateurs'),
                'cle_usb'=>$articleRepository->findCategoryTitle('clé usb'),
                'claviers_souris'=>$articleRepository->findCategoryTitle('claviers et souris'),
                'imprimante_accessoires'=>$articleRepository->findCategoryTitle('imprimante et accessoires'),
                'all'=>$articleRepository->findRand(20),
                'tendances'=>$articleRepository->findBy([
                    'etat'=>'Tendance',
                    'enabled'=>true
                ]),
                'top'=>$articleRepository->findBy([
                    'etat'=>'Top',
                    'enabled'=>true
                ])
            ]
        ]);
    }
    /**
     * @Route("/test",  name="test")
     */
    public function test(UserRepository $userRepository, ArticleRepository $articleRepository): Response
    {   
        $article = $articleRepository->find(66);
        dump(
            $articleRepository->isFavoris($this->getUser(),$article)
        );
        return dd('');
    }
}