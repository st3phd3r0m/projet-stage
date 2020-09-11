<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/messages")
 */
class MessagesController extends AbstractController
{
    /**
     * @Route("/", name="messages_index", methods={"GET"})
     */
    public function index(MessagesRepository $messagesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($request->get('productId')) {
            $messagesBuffer = $messagesRepository->findBy(['product' => $request->get('productId')]);
        } else if( $request->get('search')){

            //Récupération des données de la requete GET
            $criteria = $request->query->all();
            //Appel de la méthode de requete DQL de recherche
            $messagesBuffer = $messagesRepository->searchFilter($criteria);
    
        } else {
            $messagesBuffer = $messagesRepository->findBy([], ['sent_at' => 'DESC']);
        }

        $messages = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $messagesBuffer,
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('messages/index.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/{id}", name="messages_show", methods={"GET"})
     */
    public function show(Messages $message): Response
    {
        return $this->render('messages/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/{id}", name="messages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Messages $message): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Le message a bien été supprimé.');
        }

        return $this->redirectToRoute('messages_index');
    }
}
