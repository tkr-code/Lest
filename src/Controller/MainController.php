<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ArticleSearch;
use App\Entity\Client;
use App\Entity\User;
use App\Form\ArticleSearchType;
use App\Repository\ClientRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends AbstractController
{
    /**
     * @Route("/change-lang/{locale}", name="lang")
     */
    public function changeLocale($locale, Request $request)
    {
    
        $locale = $request->attributes->get('locale');
        
        $request->getSession()->set('_locale', $locale);
        
        $request->setLocale($request->getSession()->get('_locale', $locale));    
        
        return $this->redirect($request->headers->get('referer'));
    }
    /**
     * @Route("gestion-user/delete/{id}/{key}", name="client_user_delete", methods={"GET"})
     */
    public function deleteUserGet(Request $request, Client $user, $key): Response
    {
        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$user
            );
        }
        if($user->getUser()->getStatus() == 'Delete'){
            
        }
        $is_valide = true;
        if($key  == $user->getUser()->getCle())
        {
            $reponse  = [
                'reponse'=>false,
            ];
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
                $reponse = [
                    'reponse'=>true,
                ];
                return $this->redirectToRoute('app_logout');
            }
            $is_valide = false;
        }
        return $this->render('delete/user/index.html.twig',[
            'user'=>$user,
            'is_valide'=>$is_valide,
        ]);
    }
    /**
     * @Route("gestion-user/js-delete/{id}/{key}", name="js_client_user_delete", methods={"POST"})
     */
    public function deleteUserPost(Request $request, Client $user, $key): Response
    {
        $reponse  = [
            'reponse'=>false,
        ];
        if($key  == $user->getUser()->getCle())
        {
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
                $reponse = [
                    'reponse'=>true,
                ];
                return new JsonResponse($reponse);
            }else{
                return new JsonResponse($reponse);
            }
        }
        return new JsonResponse($reponse);
    }
}