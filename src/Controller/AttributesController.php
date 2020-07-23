<?php

namespace App\Controller;

use App\Entity\Attributes;
use App\Form\AttributesType;
use App\Repository\AttributesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/attributes")
 */
class AttributesController extends AbstractController
{
    /**
     * @Route("/", name="attributes_index", methods={"GET"})
     */
    public function index(AttributesRepository $attributesRepository, PaginatorInterface $paginator, Request $request ): Response
    {
        $attributes = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $attributesRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('attributes/index.html.twig', [
            'attributes' => $attributes
        ]);
    }

    /**
     * @Route("/new", name="attributes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $attribute = new Attributes();
        $form = $this->createForm(AttributesType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attribute);
            $entityManager->flush();

            return $this->redirectToRoute('attributes_index');
        }

        return $this->render('attributes/new.html.twig', [
            'attribute' => $attribute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="attributes_show", methods={"GET"})
     */
    public function show(Attributes $attribute): Response
    {
        return $this->render('attributes/show.html.twig', [
            'attribute' => $attribute,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attributes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Attributes $attribute): Response
    {
        $form = $this->createForm(AttributesType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('attributes_index');
        }

        return $this->render('attributes/edit.html.twig', [
            'attribute' => $attribute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="attributes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Attributes $attribute): Response
    {
        if ($this->isCsrfTokenValid('delete'.$attribute->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attribute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('attributes_index');
    }
}
