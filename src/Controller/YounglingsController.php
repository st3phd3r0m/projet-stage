<?php

namespace App\Controller;

use App\Entity\Younglings;
use App\Form\YounglingsType;
use App\Repository\YounglingsRepository;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(YounglingsRepository $younglingsRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $younglings = $paginator->paginate(
            $younglingsRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('younglings/index.html.twig', [
            'younglings' => $younglings,
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

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos de l\'***REMOVED*** ont bien été enregistrées.');
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
    public function delete(Request $request, Younglings $youngling, Filesystem $filesystem ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$youngling->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            //Suppression du fichier miniature associé à l'***REMOVED***
            $miniature = '../public/media/cache/miniatures/images/younglings/' . $youngling->getPicture();
            //Si le fichier existe
            if ($filesystem->exists($miniature)) {
                //Alors on supprime la miniature correspondante
                $filesystem->remove($miniature);
            }

            $entityManager->remove($youngling);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Les infos de l\'***REMOVED*** ont bien été supprimées.');
        }

        return $this->redirectToRoute('younglings_index');
    }
}
