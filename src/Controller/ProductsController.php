<?php

namespace App\Controller;

use App\Entity\Attributes;
use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\AttributeGroupsRepository;
use App\Repository\AttributesRepository;
use App\Repository\ImagesRepository;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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

        if ($request->get('categoryId')) {
            $productsBuffer = $productsRepository->findBy(['category' => $request->get('categoryId')], ['created_at' => 'DESC']);
        } else {
            $productsBuffer = $productsRepository->findAll();
        }

        $products = $paginator->paginate(
            $productsBuffer,
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
    public function new(Request $request, ProductsRepository $productsRepository, AttributesRepository $attributesRepository, AttributeGroupsRepository $attributeGroupsRepository): Response
    {

        //On créé la référence du produit et on l'incrémente par rapport
        //au produit ayant la référence maximal (d'un point de vue numérique)
        //Pour celà, est utilisée une requete DQL créée de toutes pièces dans
        //productsRepository
        $reference = $productsRepository->findMaxRef();
        //Incrémentation
        $reference = sprintf('%06d', $reference[0][1] + 1);

        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère les instances de l'entité Images, instanciées lors de la collection dans le formulaire d'ajout d'images
            $images = $product->getImages();
            //Et pour chacune de ces instances,
            foreach ($images as $key => $image) {
                //on fait le lien avec l'objet product
                $image->setProduct($product);
                $images->set($key, $image);
            }

            //Ajout d'un ou des attributs au produits
            //Lors de la soumission du fomrmulaire, le mapping pour les champs des attributs est désactivé
            //On récupère les données saisies par l'utilisateur via $request
            //Si la variable 'attribute' existe et n'est pas nulle (si l'utilisateur a rempli les champs relatifs à 'attribute')...
            if (
                isset($request->request->get('products')['attribute']) &&
                !empty($request->request->get('products')['attribute'])
            ) {
                //...on récupére des attributs saisis par l'utilisateur
                $attributeValues = $request->request->get('products')['attribute'];

                //Appel de la méthode de persistement des données
                $this->addAttributesToProduct($attributeValues, $attributesRepository, $attributeGroupsRepository, $product);
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
            $product->setReference($reference);


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
    public function edit(Request $request, Products $product, Filesystem $filesystem, AttributesRepository $attributesRepository, AttributeGroupsRepository $attributeGroupsRepository): Response
    {
        //Récupération des noms de fichiers images pour suppression ultérieure des miniatures
        $images = $product->getImages();
        $oldImages = [];
        foreach ($images as $key => $image) {
            $oldImages[] = $image->getName();
        }
        //Création de formulaire
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        //Si le formulaire est soumis et valide,
        if ($form->isSubmitted() && $form->isValid()) {

            //on appelle le manager d'entité
            $entityManager = $this->getDoctrine()->getManager();

            //On récupère les instances de l'entité Images, instanciées lors de la collection dans le formulaire d'ajout d'images
            $images = $product->getImages();
            //Et pour chacune de ces instances,
            foreach ($images as $key => $image) {
                if ($image->getName() === null && $image->getImageFile() !== null) {
                    //Si l'utilisateur ajoute une image
                    //on ajoute l'objet product comme attribut de l'objet image
                    $image->setProduct($product);
                    $images->set($key, $image);
                } elseif ($image->getName() === null && $image->getImageFile() === null) {
                    //Si l'utilisateur veut la suppression d'une des images dans la collection
                    //Alors on enlève l'objet image correspondant de l'objet product 
                    $product->removeImage($image);
                    //Et on l'enlève de la bdd avec le manager d'entité
                    $entityManager->remove($image);
                } elseif ($image->getName() !== null && $image->getImageFile() === null) {
                    //Si l'utilisateur veut garder l'image dans la collection
                    //on ajoute l'objet product comme attribut de l'objet image
                    $image->setProduct($product);
                    $images->set($key, $image);
                }
            }

            //On enlève tous les attributs de l'objet product avant de les remplacer par la liste des attributs collectés lors de la soumission du formulaire productsType
            foreach ($product->getAttribute() as $value) {
                $product->removeAttribute($value);
            }

            //Ajout d'un ou des attributs au produits
            //Lors de la soumission du fomrmulaire, le mapping pour les champs des attributs est désactivé
            //On récupère les données saisies par l'utilisateur via $request
            //Si la variable 'attribute' existe et n'est pas nulle (si l'utilisateur a rempli les champs relatifs à 'attribute')...
            if (
                isset($request->request->get('products')['attribute']) &&
                !empty($request->request->get('products')['attribute'])
            ) {
                //...on récupére des attributs saisis par l'utilisateur
                $attributeValues = $request->request->get('products')['attribute'];
                // dd($attributeValues);
                //Appel de la méthode de persistement des données
                $this->addAttributesToProduct($attributeValues, $attributesRepository, $attributeGroupsRepository, $product);
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
    public function delete(Request $request, Products $product, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {


            // dd($request->request->get('delete_related'));


            $entityManager = $this->getDoctrine()->getManager();

            //Suppression des images et des miniatures associés au produit
            $images = $product->getImages();
            foreach ($images as $image) {
                $miniature = '../public/media/cache/miniatures/images/products/' . $image->getName();
                //Si le fichier existe
                if ($filesystem->exists($miniature)) {
                    //Alors on supprime la miniature correspondante
                    $filesystem->remove($miniature);
                }
                $product->removeImage($image);
                $entityManager->remove($image);
            }

            //Commentaires associés au produit
            $comments = $product->getComments();
            foreach ($comments as $comment) {
                //Rupture entre les commentaires et le produit
                $product->removeComment($comment);
                //Suppression des commentaires si l'utilisateur le décide
                if($request->request->get('delete_related')==="true"){
                    $entityManager->remove($comment);
                }
            }

            //Messages associés au produit
            $messages = $product->getMessages();
            foreach ($messages as $message) {
                //Rupture entre les messages et le produit
                $product->removeMessage($message);
                //Suppression des messages si l'utilisateur le décide
                if($request->request->get('delete_related')==="true"){
                    $entityManager->remove($message);
                }
            }

            $entityManager->remove($product);
            $entityManager->flush();

            //Envoi d'un message utilisateur
            $this->addFlash('success', 'La sortie a bien été supprimée.');
        }

        return $this->redirectToRoute('products_index');
    }

    /**
     * Méthode qui fait persister les attributs saisis par l'utilisateur lors de la création/édition d'un produit
     *
     * @param array $attributeValues
     * @param AttributesRepository $attributesRepository
     * @param AttributeGroupsRepository $attributeGroupsRepository
     * @param Products $product
     * @return void
     */
    public function addAttributesToProduct($attributeValues, AttributesRepository $attributesRepository, AttributeGroupsRepository $attributeGroupsRepository, Products $product)
    {
        //on appelle le manager d'entité
        $entityManager = $this->getDoctrine()->getManager();

        //L'utilisateur peut choisir plusieurs attributs dans le formulaire. On boucle sur la variable qui est un array (généré par un collectionType)
        foreach ($attributeValues as $attributeValue) {

            if (!empty($attributeValue['value'])) {
                //Est-ce le contenu ('value') de l'attribut est en bdd ?
                $attribute = $attributesRepository->findOneBy(['value' => $attributeValue['value']]);
                //Si ce n'est pas le cas, on instancie Attributes : on cré un nouvel Attribut
                if (!$attribute) {
                    $attribute = new Attributes();
                    $attributeGroup = $attributeGroupsRepository->find($attributeValue['attribute_group']);
                    $attribute->setAttributeGroup($attributeGroup);
                    $attribute->setName($attributeValue['name']);
                    $attribute->setValue($attributeValue['value']);
                }
                //Dans tous les cas, on fait persister l'attribut en bdd et on l'ajoute au produit (qui est une intanciation de l'entité Products)
                $entityManager->persist($attribute);
                $product->addAttribute($attribute);
            }
        }
    }
}
