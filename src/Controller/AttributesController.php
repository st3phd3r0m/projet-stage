<?php

namespace App\Controller;

use App\Entity\Attributes;
use App\Form\AttributesType;
use App\Repository\AttributeGroupsRepository;
use App\Repository\AttributesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/attributes")
 */
class AttributesController extends AbstractController
{
    /**
     * @Route("/", name="attributes_index", methods={"GET"})
     */
    public function index(AttributesRepository $attributesRepository, AttributeGroupsRepository $attributeGroupsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($request->get('attributeGroupId')) {
            $attributesBuffer = $attributesRepository->findBy(['attribute_group' => $request->get('attributeGroupId')]);

        } else if( $request->get('search') || $request->get('attributeGroupFilter') ){

            //Récupération des données de la requete GET
            $criteria = $request->query->all();
            //Appel de la méthode de requete DQL de recherche
            $attributesBuffer = $attributesRepository->searchFilter($criteria);

        } else {
            $attributesBuffer = $attributesRepository->findAll();
        }

        $attributes = $paginator->paginate(
            $attributesBuffer,
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('attributes/index.html.twig', [
            'attributes' => $attributes,
            'attributeGroups'=>$attributeGroupsRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="attributes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $attribute = new Attributes();
        $form = $this->createForm(AttributesType::class, $attribute,[
            'embeddedToProductForm'=>false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attribute);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'attribut a bien été créé.');
            return $this->redirectToRoute('attributes_index');
        }

        return $this->render('attributes/new.html.twig', [
            'attribute' => $attribute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attributes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Attributes $attribute): Response
    {
        $form = $this->createForm(AttributesType::class, $attribute,[
            'embeddedToProductForm'=>false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attribute);
            $entityManager->flush();

            $this->getDoctrine()->getManager()->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'attribut a bien été modifié.');
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
        if ($this->isCsrfTokenValid('delete' . $attribute->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attribute);
            $entityManager->flush();
            
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'attribut a bien été supprimé.');
        }

        return $this->redirectToRoute('attributes_index');
    }
}
