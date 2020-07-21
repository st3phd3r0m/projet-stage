<?php

namespace App\Controller;

use App\Entity\Languages;
use App\Form\LanguagesType;
use App\Repository\LanguagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/languages")
 */
class LanguagesController extends AbstractController
{
    /**
     * @Route("/", name="languages_index", methods={"GET"})
     */
    public function index(LanguagesRepository $languagesRepository): Response
    {
        return $this->render('languages/index.html.twig', [
            'languages' => $languagesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="languages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $language = new Languages();
        $form = $this->createForm(LanguagesType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($language);
            $entityManager->flush();

            return $this->redirectToRoute('languages_index');
        }

        return $this->render('languages/new.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="languages_show", methods={"GET"})
     */
    public function show(Languages $language): Response
    {
        return $this->render('languages/show.html.twig', [
            'language' => $language,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="languages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Languages $language): Response
    {
        $form = $this->createForm(LanguagesType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('languages_index');
        }

        return $this->render('languages/edit.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="languages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Languages $language): Response
    {
        if ($this->isCsrfTokenValid('delete'.$language->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($language);
            $entityManager->flush();
        }

        return $this->redirectToRoute('languages_index');
    }
}
