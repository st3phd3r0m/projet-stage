<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories")
 */
class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="categories_index", methods={"GET"})
     */
    public function index(CategoriesRepository $categoriesRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $categories = $paginator->paginate(
            //Appel de la méthode de requete DQL de recherche
            $categoriesRepository->findBy([], ['created_at' => 'DESC']),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/new", name="categories_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère les instances de l'entité Images, instanciées lors de la collection dans le formulaire d'ajout d'images
            $images = $category->getImages();
            //Et pour chacune de ces instances,
            foreach ($images as $key => $image) {
                //on fait le lien avec l'objet category
                $image->setProduct($category);
                $images->set($key, $image);
            }

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get("keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $category->setKeywords($keywords);

            $keywords = $form->get("meta_tag_keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $category->setMetaTagKeywords($keywords);

            $category->setCreatedAt(new \DateTime('now'));
            $category->setUpdatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'Nouvelle catégorie créée.');
            return $this->redirectToRoute('categories_index');
        }

        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categories_show", methods={"GET"})
     */
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categories_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categories $category, Filesystem $filesystem): Response
    {
        //Récupération des noms de fichiers images pour suppression ultérieure des miniatures
        $images = $category->getImages();
        $oldImages = [];
        foreach ($images as $key => $image) {
            $oldImages[] = $image->getName();
        }
        //Création de formulaire
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on appelle le manager d'entité
            $entityManager = $this->getDoctrine()->getManager();

            //On récupère les instances de l'entité Images, instanciées lors de la collection dans le formulaire d'ajout d'images
            $images = $category->getImages();
            //Et pour chacune de ces instances,
            foreach ($images as $key => $image) {
                if ($image->getName() === null && $image->getImageFile() !== null) {
                    //Si l'utilisateur ajoute une image
                    //on ajoute l'objet category comme attribut de l'objet image
                    $image->setCategory($category);
                    $images->set($key, $image);
                } elseif ($image->getName() === null && $image->getImageFile() === null) {
                    //Si l'utilisateur veut la suppression d'une des images dans la collection
                    //Alors on enlève l'objet image correspondant de l'objet category 
                    $category->removeImage($image);
                    //Et on l'enlève de la bdd avec le manager d'entité
                    $entityManager->remove($image);
                } elseif ($image->getName() !== null && $image->getImageFile() === null) {
                    //Si l'utilisateur veut garder l'image dans la collection
                    //on ajoute l'objet category comme attribut de l'objet image
                    $image->setCategory($category);
                    $images->set($key, $image);
                }
            }

            //Récupération des mots-clés en tant que chaine de caractères et séparation en array avec un délimiteur ";"
            $keywords = $form->get("keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $category->setKeywords($keywords);

            $keywords = $form->get("meta_tag_keywords")->getData();
            $keywords = explode("#", $keywords);
            $keywords = array_filter($keywords);
            $category->setMetaTagKeywords($keywords);

            $category->setUpdatedAt(new \DateTime('now'));

            $entityManager->flush();

            //Suppression des fichiers miniatures
            foreach ($oldImages as $oldImage) {
                $miniature = '../public/media/cache/miniatures/images/products/' . $oldImage;
                //On supprime la miniature correspondante à l'image
                if ($filesystem->exists($miniature)) {
                    //Alors on supprime la miniature correspondante
                    $filesystem->remove($miniature);
                }
            }

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La catégorie a bien été modifiée.');
            return $this->redirectToRoute('categories_index');
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categories_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categories $category, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            //Suppression des images et des miniatures associés à la categorie
            $images = $category->getImages();
            foreach ($images as $image) {
                $miniature = '../public/media/cache/miniatures/images/products/' . $image->getName();
                //Si le fichier existe
                if ($filesystem->exists($miniature)) {
                    //Alors on supprime la miniature correspondante
                    $filesystem->remove($miniature);
                }
                $category->removeImage($image);
                $entityManager->remove($image);
            }

            //Suppression des produits associés à la ctégorie
            $products = $category->getProducts();
            foreach ($products as $product) {
                $category->removeProduct($product);
                // $entityManager->remove($product);
            }


            $entityManager->remove($category);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        }

        return $this->redirectToRoute('categories_index');
    }
}
