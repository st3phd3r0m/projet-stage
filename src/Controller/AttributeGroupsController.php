<?php

namespace App\Controller;

use App\Entity\AttributeGroups;
use App\Form\AttributeGroupsType;
use App\Repository\AttributeGroupsRepository;
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
    public function index(AttributeGroupsRepository $attributeGroupsRepository): Response
    {
        return $this->render('attribute_groups/index.html.twig', [
            'attribute_groups' => $attributeGroupsRepository->findAll(),
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
        if ($this->isCsrfTokenValid('delete'.$attributeGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attributeGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('attribute_groups_index');
    }
}
