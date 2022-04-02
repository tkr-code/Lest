<?php

namespace App\Controller;

use App\Entity\ArticleOption;
use App\Form\ArticleOptionType;
use App\Repository\ArticleOptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/article-option")
 */
class ArticleOptionController extends AbstractController
{
    /**
     * @Route("/", name="article_option_index", methods={"GET"})
     */
    public function index(ArticleOptionRepository $articleOptionRepository): Response
    {
        return $this->render('article_option/index.html.twig', [
            'article_options' => $articleOptionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_option_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $articleOption = new ArticleOption();
        $form = $this->createForm(ArticleOptionType::class, $articleOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($articleOption);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article_option/new.html.twig', [
            'article_option' => $articleOption,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="article_option_show", methods={"GET"})
     */
    public function show(ArticleOption $articleOption): Response
    {
        return $this->render('article_option/show.html.twig', [
            'article_option' => $articleOption,
        ]);
    }

    /**
     * @Route("/{id}/edit/{idArticle}/article", name="article_option_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ArticleOption $articleOption,$idArticle): Response
    {
        $form = $this->createForm(ArticleOptionType::class, $articleOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_edit', ['id'=>$idArticle], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article_option/edit.html.twig', [
            'article_option' => $articleOption,
            'formOption' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete/{idArticle}/article", name="article_option_delete", methods={"POST"})
     */
    public function delete(Request $request, ArticleOption $articleOption, $idArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$articleOption->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($articleOption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_edit', ['id'=>$idArticle], Response::HTTP_SEE_OTHER);
    }
}