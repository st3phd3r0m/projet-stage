<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Form\PagesType;
use App\Repository\PagesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/pages")
 */
class PagesController extends AbstractController
{
    /**
     * @Route("/", name="pages_index", methods={"GET"})
     */
    public function index(PagesRepository $pagesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($request->get('userId')) {
            $pagesBuffer = $pagesRepository->findBy(['user' => $request->get('userId')], ['created_at' => 'DESC']);
        } else {
            $pagesBuffer = $pagesRepository->findBy([], ['created_at' => 'DESC']);
        }

        $pages = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $pagesBuffer,
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('pages/index.html.twig', [
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/new", name="pages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $page = new Pages();
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get("keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $page->setKeywords($keywords);

            $keywords = $form->get("meta_tag_keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $page->setMetaTagKeywords($keywords);

            $page->setCreatedAt(new \DateTime('now'));
            $page->setUpdatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('pages_index');
        }

        return $this->render('pages/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pages_show", methods={"GET"})
     */
    public function show(Pages $page): Response
    {
        return $this->render('pages/show.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pages $page): Response
    {
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get("keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $page->setKeywords($keywords);

            $keywords = $form->get("meta_tag_keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $page->setMetaTagKeywords($keywords);

            $page->setUpdatedAt(new \DateTime('now'));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pages_index');
        }

        return $this->render('pages/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Pages $page): Response
    {
        if ($this->isCsrfTokenValid('delete' . $page->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pages_index');
    }
}
