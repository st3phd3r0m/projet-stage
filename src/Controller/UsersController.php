<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $usersRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('users/index.html.twig', [
            'users' => $users
        ]);
    }
    
    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $user = new Users;
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($user);
             $entityManager->flush();

             return $this->redirectToRoute('users_index');
         }

         return $this->render('users/new.html.twig', [
             'user' => $user,
             'form' => $form->createView(),
         ]);
    }

    /**
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }



    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }
}
