<?php

namespace App\Controller;

use App\Entity\Younglings;
use App\Form\YounglingsType;
use App\Repository\YounglingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/younglings")
 */
class YounglingsController extends AbstractController
{
    /**
     * @Route("/", name="younglings_index", methods={"GET"})
     */
    public function index(YounglingsRepository $younglingsRepository): Response
    {
        return $this->render('younglings/index.html.twig', [
            'younglings' => $younglingsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="younglings_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $youngling = new Younglings();
        $form = $this->createForm(YounglingsType::class, $youngling);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $youngling->setUpdatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($youngling);
            $entityManager->flush();

            return $this->redirectToRoute('younglings_index');
        }

        return $this->render('younglings/new.html.twig', [
            'youngling' => $youngling,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="younglings_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Younglings $youngling, Filesystem $filesystem): Response
    {
        //Récupération des noms de fichiers images pour suppression ultérieure des miniatures
        $oldImage = $youngling->getPicture();

        $form = $this->createForm(YounglingsType::class, $youngling);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $youngling->setUpdatedAt(new \DateTime('now'));

            $this->getDoctrine()->getManager()->flush();

            $miniature = '../public/media/cache/miniatures/images/younglings/' . $oldImage;
            //On supprime la miniature correspondante à l'image
            if ($filesystem->exists($miniature)) {
                //Alors on supprime la miniature correspondante
                $filesystem->remove($miniature);
            }

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos de l\'***REMOVED*** ont bien été modifiée.');

            return $this->redirectToRoute('younglings_index');
        }

        return $this->render('younglings/edit.html.twig', [
            'youngling' => $youngling,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="younglings_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Younglings $youngling): Response
    {
        if ($this->isCsrfTokenValid('delete'.$youngling->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($youngling);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos de l\'***REMOVED*** ont bien été supprimées.');
        }

        return $this->redirectToRoute('younglings_index');
    }
}
