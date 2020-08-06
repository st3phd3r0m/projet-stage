<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Products;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/comments")
 */
class CommentsController extends AbstractController
{
    /**
     * @Route("/", name="comments_index", methods={"GET"})
     */
    public function index(CommentsRepository $commentsRepository, PaginatorInterface $paginator, Request $request): Response
    {

        if ($request->get('productId')) {
            $commentsBuffer = $commentsRepository->findBy(['product' => $request->get('productId')], ['created_at' => 'DESC']);
        } else if( $request->get('search') || $request->get('isModaratedFilter') ){ 
            
            //Récupération des données de la requete GET
            $criteria = $request->query->all();
            //Appel de la méthode de requete DQL de recherche
            $commentsBuffer = $commentsRepository->searchFilter($criteria);

        } else {
            $commentsBuffer = $commentsRepository->findBy([], ['created_at' => 'DESC']);
        }

        $comments = $paginator->paginate(
            $commentsBuffer,
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('comments/index.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/{id}", name="comments_show", methods={"GET"})
     */
    public function show(Comments $comment): Response
    {
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comments_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comments $comment): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->remove('pseudo');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setIsModerated(true);

            $this->getDoctrine()->getManager()->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'avis client a bien été modifié.');
            return $this->redirectToRoute('comments_index');
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comments_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comments $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'avis client a bien été supprimé');
        }

        return $this->redirectToRoute('comments_index');
    }
}
