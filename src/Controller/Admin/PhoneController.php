<?php

namespace App\Controller\Admin;

use App\Entity\Phone;
use App\Form\PhoneType;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/phone")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/", name="admin_phone_index", methods={"GET"})
     */
    public function index(PhoneRepository $phoneRepository): Response
    {
        return $this->render('admin/phone/index.html.twig', [
            'phones' => $phoneRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_phone_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $phone = new Phone();
        $phone->setUser($this->getUser());
        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            dd($phone);
            $entityManager->persist($phone);
            $entityManager->flush();

            return $this->redirectToRoute('admin_phone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/phone/new.html.twig', [
            'phone' => $phone,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_phone_show", methods={"GET"})
     */
    public function show(Phone $phone): Response
    {
        return $this->render('admin/phone/show.html.twig', [
            'phone' => $phone,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_phone_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Phone $phone): Response
    {
        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_phone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/phone/edit.html.twig', [
            'phone' => $phone,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_phone_delete", methods={"POST"})
     */
    public function delete(Request $request, Phone $phone): Response
    {
        if ($this->isCsrfTokenValid('delete'.$phone->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($phone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_phone_index', [], Response::HTTP_SEE_OTHER);
    }
}
