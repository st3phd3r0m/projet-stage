<?php

namespace App\Controller;

use App\Entity\People;
use App\Form\PeopleType;
use App\Repository\PeopleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/people")
 */
class PeopleController extends AbstractController
{
    /**
     * @Route("/", name="people_index", methods={"GET"})
     */
    public function index(PeopleRepository $peopleRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $people = $paginator->paginate(
            $peopleRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('people/index.html.twig', [
            'people' => $people,
        ]);
    }

    /**
     * @Route("/new", name="people_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->forward('App\Controller\PagesController::newFirmPage', [
            'slug'  => 'qui-sommes-nous'
        ]);

        $person = new People();
        $form = $this->createForm(PeopleType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $person->setUpdatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($person);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouveau collaborateur créé.');

            return $this->redirectToRoute('people_index');
        }

        return $this->render('people/new.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="people_show", methods={"GET"})
     */
    public function show(People $person): Response
    {
        return $this->render('people/show.html.twig', [
            'person' => $person,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="people_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, People $person, Filesystem $filesystem): Response
    {
        //Récupération des noms de fichiers images pour suppression ultérieure des miniatures
        $oldImage = $person->getPicture();

        $form = $this->createForm(PeopleType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $person->setUpdatedAt(new \DateTime('now'));

            $this->getDoctrine()->getManager()->flush();

            $miniature = '../public/media/cache/miniatures/images/people/' . $oldImage;
            //On supprime la miniature correspondante à l'image
            if ($filesystem->exists($miniature)) {
                //Alors on supprime la miniature correspondante
                $filesystem->remove($miniature);
            }

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos du collaborateur ont bien été modifiée.');

            return $this->redirectToRoute('people_index');
        }

        return $this->render('people/edit.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="people_delete", methods={"DELETE"})
     */
    public function delete(Request $request, People $person, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete' . $person->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            //Suppression du fichier miniature associé à l'***REMOVED***
            $miniature = '../public/media/cache/miniatures/images/people/' . $person->getPicture();
            //Si le fichier existe
            if ($filesystem->exists($miniature)) {
                //Alors on supprime la miniature correspondante
                $filesystem->remove($miniature);
            }

            $entityManager->remove($person);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos du collaborateur ont bien été supprimées.');
        }

        return $this->redirectToRoute('people_index');
    }
}
