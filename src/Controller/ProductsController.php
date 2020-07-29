<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ImagesRepository;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/products")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="products_index", methods={"GET"})
     */
    public function index(ProductsRepository $productsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $productsRepository->findAll(), //findBy([], ['created_at' => 'DESC']),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );
        return $this->render('products/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/new", name="products_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProductsRepository $productsRepository): Response
    {

        $maxRef = $productsRepository->findMaxRef();
        $maxRef = sprintf('%06d', $maxRef[0][1] + 1);

        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $product->getImages();
            foreach ($images as $key => $image) {
                $image->setProduct($product);
                $images->set($key, $image);
            }

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get("keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $product->setKeywords($keywords);

            $keywords = $form->get("meta_tag_keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $product->setMetaTagKeywords($keywords);

            $product->setCreatedAt(new \DateTime('now'));
            $product->setUpdatedAt(new \DateTime('now'));
            $product->setReference($maxRef);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouvelle sortie créée.');

            return $this->redirectToRoute('products_index');
        }

        return $this->render('products/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="products_show", methods={"GET"})
     */
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="products_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Products $product, ImagesRepository $imagesRepository ): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // dd($imagesRepository->findBy(['product'=>$product->getId()]));

            $images = $product->getImages();
            // dd($images);
            foreach ($images as $key => $image) {
                $image->setProduct($product);
                $images->set($key, $image);
            }

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get("keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $product->setKeywords($keywords);

            $keywords = $form->get("meta_tag_keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $product->setMetaTagKeywords($keywords);

            $product->setUpdatedAt(new \DateTime('now'));

            $this->getDoctrine()->getManager()->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La sortie a bien été modifiée.');
            return $this->redirectToRoute('products_index');
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="products_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Products $product, ImagesRepository $imagesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();

            $images = $imagesRepository->findBy(['product'=>$product->getId()]);
            foreach($images as $key => $image){
                $product->removeImage($image);
                $entityManager->remove($image);
            }

            $entityManager->remove($product);
            $entityManager->flush();
            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La sortie a bien été supprimée.');
        }

        return $this->redirectToRoute('products_index');
    }
}
