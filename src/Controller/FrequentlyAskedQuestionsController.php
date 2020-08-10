<?php

namespace App\Controller;

use App\Entity\FrequentlyAskedQuestions;
use App\Form\FrequentlyAskedQuestionsType;
use App\Repository\FrequentlyAskedQuestionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/frequently/asked/questions")
 */
class FrequentlyAskedQuestionsController extends AbstractController
{
    /**
     * @Route("/", name="frequently_asked_questions_index", methods={"GET"})
     */
    public function index(FrequentlyAskedQuestionsRepository $frequentlyAskedQuestionsRepository): Response
    {
        return $this->render('frequently_asked_questions/index.html.twig', [
            'frequently_asked_questions' => $frequentlyAskedQuestionsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="frequently_asked_questions_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $frequentlyAskedQuestion = new FrequentlyAskedQuestions();
        $form = $this->createForm(FrequentlyAskedQuestionsType::class, $frequentlyAskedQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($frequentlyAskedQuestion);
            $entityManager->flush();

            return $this->redirectToRoute('frequently_asked_questions_index');
        }

        return $this->render('frequently_asked_questions/new.html.twig', [
            'frequently_asked_question' => $frequentlyAskedQuestion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="frequently_asked_questions_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FrequentlyAskedQuestions $frequentlyAskedQuestion): Response
    {
        $form = $this->createForm(FrequentlyAskedQuestionsType::class, $frequentlyAskedQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('frequently_asked_questions_index');
        }

        return $this->render('frequently_asked_questions/edit.html.twig', [
            'frequently_asked_question' => $frequentlyAskedQuestion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="frequently_asked_questions_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FrequentlyAskedQuestions $frequentlyAskedQuestion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frequentlyAskedQuestion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($frequentlyAskedQuestion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('frequently_asked_questions_index');
    }
}
