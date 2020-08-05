<?php

namespace App\Controller;

use App\Entity\Languages;
use App\Form\LanguagesType;
use App\Repository\LanguagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/languages")
 */
class LanguagesController extends AbstractController
{
    /**
     * @Route("/", name="languages_index", methods={"GET"})
     */
    public function index(LanguagesRepository $languagesRepository): Response
    {
        return $this->render('languages/index.html.twig', [
            'languages' => $languagesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="languages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $language = new Languages();
        $form = $this->createForm(LanguagesType::class, $language, [
            'validation_groups' => ['new']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($language);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouvelle langue de publication créée.');
            return $this->redirectToRoute('languages_index');
        }

        return $this->render('languages/new.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="languages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Languages $language, Filesystem $filesystem): Response
    {
        $form = $this->createForm(LanguagesType::class, $language, [
            'validation_groups' => ['update'],
            'require_image' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On persiste la propriété updated_at afin de trigger les écouteurs d'évènements avec doctrine et de permettre la mise à jour du fichier image
            $language->setUpdatedAt(new \DateTime('now'));
            //Stockage de l'ancien nom de fichier image
            $oldImage = $language->getFlag();
            //persistement en bdd
            $this->getDoctrine()->getManager()->flush();
            //Stockage du nouveau nom de fichier après persistement en bdd
            $newImage = $language->getFlag();
            //Si le nom de fichier a changé
            if ($oldImage != $newImage) {
                $oldImage = '../public/media/cache/miniatures/images/languages/' . $oldImage;
                if ($filesystem->exists($oldImage)) {
                    //Alors on supprime la miniature correspondante
                    $filesystem->remove($oldImage);
                }
            }

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La langue de publication a bien été modifiée.');
            return $this->redirectToRoute('languages_index');
        }

        return $this->render('languages/edit.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="languages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Languages $language, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete' . $language->getId(), $request->request->get('_token'))) {
            //Stockage de l'ancien nom de fichier image
            $oldImage = $language->getFlag();

            $entityManager = $this->getDoctrine()->getManager();

            //Produits associés à la langue de publication
            $products = $language->getProducts();
            foreach ($products as $product) {
                //Rupture entre les produits et la langue de publication
                $language->removeProduct($product);
                //On ne supprime pas les produits associés
            }

            $entityManager->remove($language);
            $entityManager->flush();

            //Suppression de la miniature associée à la langue de publication
            $oldImage = '../public/media/cache/miniatures/images/languages/' . $oldImage;
            //Si le fichier existe
            if ($filesystem->exists($oldImage)) {
                //Alors on le supprime
                $filesystem->remove($oldImage);
            }

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La langue de publication a bien été supprimée.');
        }

        return $this->redirectToRoute('languages_index');
    }
}
