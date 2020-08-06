<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Form\PagesType;
use App\Repository\PagesRepository;
use App\Repository\UsersRepository;
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
    public function index(PagesRepository $pagesRepository, UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($request->get('userId')) {
            $pagesBuffer = $pagesRepository->findBy(['user' => $request->get('userId')], ['created_at' => 'DESC']);
        } else if( $request->get('search') || $request->get('usersFilter') ){

            //Récupération des données de la requete GET
            $criteria = $request->query->all();
            //Appel de la méthode de requete DQL de recherche
            $pagesBuffer = $pagesRepository->searchFilter($criteria);

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
            'pages' => $pages,
            'users'=> $usersRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="pages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if ($this->getUser()) {
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

                $page->setUser($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($page);
                $entityManager->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre page a bien été publiée.');
                return $this->redirectToRoute('pages_index');
            }

            return $this->render('pages/new.html.twig', [
                'page' => $page,
                'form' => $form->createView(),
            ]);
        }

        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Vous devez être connecté pour publier une page.');
        return $this->redirectToRoute('pages_index');
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
        if ($this->getUser()) {
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

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre page a bien été modifiée.');
                return $this->redirectToRoute('pages_index');
            }

            return $this->render('pages/edit.html.twig', [
                'page' => $page,
                'form' => $form->createView(),
            ]);
        }

        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Vous devez être connecté pour éditer une page.');
        return $this->redirectToRoute('pages_index');
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

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La publication a bien été supprimée.');
        }

        return $this->redirectToRoute('pages_index');
    }
}
