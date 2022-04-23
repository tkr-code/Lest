<?php

namespace App\Controller\Main;

use App\Entity\User;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FavorisController extends AbstractController
{
    /**
     * @Route("/favoris", name="favoris_index")
     */
    public function index(): Response
    {
        dump($this->getUser());
        return $this->render('lest/favoris/index.html.twig', [
            'controller_name' => 'FavorisController',
        ]);
    }
    /**
     * @Route("/favoris/add/{id}", name="favoris_add")
     */
    public function add(Article $article,ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();
        if($user){
            $user->addFavori($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success',"L'article à été ajouté dans la liste des favoris");
           return $this->redirectToRoute('favoris_index',[]);
        }
        return $this->render('lest/favoris/index.html.twig', [
            'controller_name' => 'FavorisController',
        ]);
    }
    /**
     * @Route("/favoris/add-ajax", name="favoris_add_ajax", methods="POST")
     */
    public function addAjax(Request $request, ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();
        if($user){
            $id =  $request->get('id');
            if(!empty($id)){
                $article = $articleRepository->find($id);
                $user->addFavori($article);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return new JsonResponse(true);
            }
            else{
                return new JsonResponse(false);
            }
        }
        return new JsonResponse(false);

    }

    /**
     * @Route("/favoris/remove/{id}", name="favoris_remove")
     */
    public function remove(Article $article): Response
    {
        $user = $this->getUser();
        if($user){
            $user->removeFavori($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success',"L'article à été supprimé des favoris");
           return $this->redirectToRoute('favoris_index',[]);
        }
        
        return $this->render('lest/favoris/index.html.twig', [
            'controller_name' => 'FavorisController',
        ]);
    }
    /**
     * @Route("/favoris/remove-ajax", name="favoris_remove_ajax")
     */
    public function removeAjax(ArticleRepository $articleRepository, Request $request): Response
    {
        $user = $this->getUser();
        if($user){
            $id =  $request->get('id');
            if(!empty($id)){
                $article = $articleRepository->find($id);
                $user->removeFavori($article);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return new JsonResponse(true);
            }
        }
        return new JsonResponse(false);
    }
}
