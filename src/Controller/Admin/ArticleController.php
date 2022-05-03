<?php

namespace App\Controller\Admin;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\ArticleOption;
use App\Form\ArticleOptionType;
use App\Repository\ArticleOptionRepository;
use App\Repository\ArticleRepository;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use function PHPUnit\Framework\fileExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("admin/article")
 */
class ArticleController extends AbstractController
{
    private $em;
    private $translator;
    public function __construct(EntityManagerInterface $entityManagerInterface, TranslatorInterface $translatorInterface)
    {
        $this->em = $entityManagerInterface;
        $this->translator = $translatorInterface;
    }
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(BrandRepository $brandRepository, ArticleRepository $articleRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'articlesOn' => $articleRepository->findAllOn(),
            'articlesOff' => $articleRepository->findAllOff(),
            'articlesTop' => $articleRepository->findEtat('top'),
        ]);
    }
    /**
     * @Route("/select/brand", name="article_brand_category_ajax", methods={"POST"})
     */
    public function brandToCategoryAjax(Request $request, CategoryRepository $categoryRepository ){
        $reponse = [
            'reponse'=>false
        ];
        $id = $request->request->get('id');
        if(!empty($id)){
            $category  = $categoryRepository->find($id);
            if($category)
            {
                $brands = $category->getBrands();
                $reponse =[
                    'reponse'=>true,
                    'content'=>$this->render('admin/article/ajax/select_brand.html.twig',['brands'=>$brands])->getContent()
                ];
                
                return new JsonResponse($reponse);
            }
        }
        return new JsonResponse($reponse);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function form(Request $request, Article $article = null, TranslatorInterface $translator, BrandRepository $brandRepository): Response
    {
        
        $action = 'Update';
        $action_text = 'Update ';
        if(!$article){
            $action = 'Save';
            $action_text = 'Create new ';
            $article = new Article();
            $article->setEnabled(true)->setTitle('produit 1')->setDescription('produit 1 description')
            ->setPrice('1500000')->setBuyingPrice('120000')->setQuantity(5)->setLabel('New');
            $formOption = null;
        }else{
                $articleOption = new ArticleOption();
                $formOption = $this->createForm(ArticleOptionType::class, $articleOption);
                $formOption->handleRequest($request);
                if(!$articleOption->getId()){
                    $articleOption->setArticle($article);
                }
        if ($formOption->isSubmitted() && $formOption->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($articleOption);
            $entityManager->flush();

            return $this->redirectToRoute('article_edit', ['id'=>$article->getId()], Response::HTTP_SEE_OTHER);
        }
        }

        $action_text .= 'Article'; 
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getClickedButton());

            //on recupere les images transmise
            $images = $form->get('images')->getData();

            foreach($images as $image)
            {
                //om gener un nouveau nom de fichier
                $fichier = md5(uniqid()). '.'.$image->guessExtension();

                //on copie le fichier dans le dosiier uploads
                $image->move(
                    $this->getParameter('article_images_directory'),
                    $fichier
                );
                //on stocke l'image dans la base de donnees 
                $img = new Image();
                $img->setName($fichier);
                $article->addImage($img);
            }
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }else{
                $article->setUpdatedAt(new \DateTime());
            }
            $article->setQtyReel($article->getQuantity());
            $idBrand = $request->request->get('brand');
            if($idBrand){
                $brand = $brandRepository->find($idBrand);
                if($brand){
                    $article->setBrand($brand);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $message = ($action == 'Save') ? $translator->trans('Article cree') : $translator->trans('Article modifier');
            $this->addFlash('success',$message);
            return $this->redirectToRoute('article_new_add_option', ['id'=>$article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/new.html.twig', [
            'article' => $article,
            'action'=>$action,
            'action_text'=>$action_text,
            'form' => $form,
            'formOption' => $formOption,
            'parent_page'=>'Produit'
        ]);
    }
    
     /**
     * @Route("/new/add-option/{id}", name="article_new_add_option", methods={"GET","POST"})
     */
    public function newOption(Article $article = null):Response
    {
        return $this->renderForm('admin/article/new_add_option.html.twig', [
            'article' => $article
        ]);
    }
     /**
     * @Route("/new/add-option-ajax", name="article_new_add_option_ajax", methods={"POST"})
     */
    public function newOptionAjax(ArticleRepository $articleRepository, ArticleOptionRepository $articleOptionRepository, Request $request):Response
    {
        $reponse = [
            'reponse'=>false,
            'error'=>'Erreur : 500'
        ];
        
        $id_article = $request->request->get('id_article');
        $nom = $request->request->get('nom');
        $valeur = $request->request->get('valeur');
        $articleOption = new ArticleOption();
        $form = $this->createForm(ArticleOptionType::class,$articleOption);
        if($nom && $valeur && $id_article  ){
            $article = $articleRepository->find($id_article);
            if($article){
                $entityManager = $this->getDoctrine()->getManager();
                $articleOption->setArticle($article)->setTitle($nom)->setContent($valeur);
                if($articleOptionRepository->findOneBy([
                    'title'=>$articleOption->getTitle()
                ]))
                {
                    $reponse =[
                        'reponse'=>false,
                        'error'=>'Cette valeur existe !'
                    ];
                    return new JsonResponse($reponse);
                }
                $entityManager->persist($articleOption);
                $entityManager->flush();
                $reponse = [
                    'reponse'=>true,
                    'error'=>'Erreur: 500 ',
                    'id'=>$articleOption->getId()
                ];
                return new JsonResponse($reponse);
            }
        }
        return new JsonResponse($reponse);
    }

    /**
     * @Route("/nouveau-produit", name="article_new_produit", methods={"GET","POST"})
     * @Route("/{id}/edit-produit", name="article_edit_produit", methods={"GET","POST"})
     */
    public function newProduit(): Response
    {
        return $this->renderForm('admin/article/lest.html.twig', []);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article, int $id): Response
    {
        if (!$article) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }


    /**
     * @Route("/{id}", name="article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach($article->getImages()->getValues() as $image){
                $path = $this->getParameter('article_images_directory').'/'.$image->getName();
                if(file_exists($path)){
                    unlink($path);
                }
            }
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('success','Deleted item');

        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * @Route("/delete/image/{id}", name="article_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Image $image,Request $request){
        $data = json_decode($request->getContent(),true);
        //om verifi si le token est valide 
        if($this->isCsrfTokenValid('delete'.$image->getId(),$data['_token'])){
            $path = $this->getParameter('article_images_directory').'/'.$image->getName();
            if(file_exists($path)){
                unlink($path);
            }
            $this->em->remove($image);
            $this->em->flush();
            return new JsonResponse(['success'=>1]);
        }else{
            return new JsonResponse(['error'=>'Token invalide'],400);
        }
    }

}