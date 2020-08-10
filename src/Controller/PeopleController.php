<?php

namespace App\Controller;

use App\Entity\People;
use App\Form\PeopleType;
use App\Repository\PeopleRepository;
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
    public function index(PeopleRepository $peopleRepository): Response
    {
        return $this->render('people/index.html.twig', [
            'people' => $peopleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="people_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
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
    public function delete(Request $request, People $person): Response
    {
        if ($this->isCsrfTokenValid('delete' . $person->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($person);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos du collaborateur ont bien été supprimées.');
        }

        return $this->redirectToRoute('people_index');
    }
}
