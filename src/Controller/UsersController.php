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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $user = new Users;
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'utilisateur a bien été ajouté en base de données');

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les informations de l\'utilisateur ont bien été modifiées');

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/change/password", name="users_change_password", methods={"GET","POST"})
     */
    public function changePassword(Request $request, Users $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser()->getUserName() === $user->getUsername()) {

            $form = $this->createForm(UsersType::class, $user);
            $form->remove('email');
            $form->remove('firstname');
            $form->remove('lastname');
            $form->remove('roles');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $this->getDoctrine()->getManager()->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                return $this->redirectToRoute('users_index');
            } else if ($form->isSubmitted() && !$form->isValid()) {
                //Envoi d'un message utilisateur
                $this->addFlash('fail', 'Formulaire incomplet ou invalide.');
            }

            return $this->render('users/edit.html.twig', [
                'changepassword' => true,
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }

        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Vous devez être connecté en tant que ' . $user->getFirstName() . ' ' . $user->getLastName() . ' pour modifier le mot de passe associé à ce compte.');
        return $this->redirectToRoute('users_index');
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
     * @Route("/{id}", name="users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            //Publications associés à l'utilisateur
            $pages = $user->getPages();
            foreach ($pages as $page) {
                //Rupture entre les publications et l'utilisateur
                $user->removePage($page);
                //On ne supprime pas les publications associées
            }

            $entityManager->remove($user);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé de la base de données');
        }

        return $this->redirectToRoute('users_index');
    }
}
