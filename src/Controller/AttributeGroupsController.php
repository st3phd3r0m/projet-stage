<?php

namespace App\Controller;

use App\Entity\AttributeGroups;
use App\Form\AttributeGroupsType;
use App\Repository\AttributeGroupsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/attribute/groups")
 */
class AttributeGroupsController extends AbstractController
{
    /**
     * @Route("/", name="attribute_groups_index", methods={"GET"})
     */
    public function index(AttributeGroupsRepository $attributeGroupsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $attribute_groups = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $attributeGroupsRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('attribute_groups/index.html.twig', [
            'attribute_groups' => $attribute_groups,
        ]);
    }


    /**
     * @Route("/new", name="attribute_groups_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $attributeGroup = new AttributeGroups();
        $form = $this->createForm(AttributeGroupsType::class, $attributeGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attributeGroup);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le groupe d\'attribut a bien été créé.');
            return $this->redirectToRoute('attribute_groups_index');
        }

        return $this->render('attribute_groups/new.html.twig', [
            'attribute_group' => $attributeGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attribute_groups_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AttributeGroups $attributeGroup): Response
    {
        $form = $this->createForm(AttributeGroupsType::class, $attributeGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le groupe d\'attribut a bien été modifié.');
            return $this->redirectToRoute('attribute_groups_index');
        }

        return $this->render('attribute_groups/edit.html.twig', [
            'attribute_group' => $attributeGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="attribute_groups_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AttributeGroups $attributeGroup): Response
    {
        if ($this->isCsrfTokenValid('delete' . $attributeGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attributeGroup);
            $entityManager->flush();
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le groupe d\'attribut a bien été supprimé.');
        }

        return $this->redirectToRoute('attribute_groups_index');
    }
}
