<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Comments;
use App\Entity\Messages;
use App\Entity\Pages;
use App\Entity\Products;
use App\Form\CommentsType;
use App\Form\MessagesType;
use App\Repository\AttributesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\FrequentlyAskedQuestionsRepository;
use App\Repository\LanguagesRepository;
use App\Repository\PagesRepository;
use App\Repository\PeopleRepository;
use App\Repository\ProductsRepository;
use App\Repository\YounglingsRepository;
use App\Twig\AppExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="redirect_to_Home")
     */
    public function redirectToHome()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/acceuil", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/home/{slug}", name="foreign")
     */
    public function foreignIndex()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/send/email", name="send_email", methods={"GET","POST"})
     */
    public function sendEmail(Request $request, MailerInterface $mailer)
    {

        if ($request->getMethod() == 'POST') {

            $post = $request->request->All();

            if ($this->isFormFilled($post) && $this->isFormValid($post)) {

                //On vérifie si l'adresse mail de l'utilisateur est valide et on renvoie une chaine de caractère si ce n'est pas le cas
                if (!filter_var(htmlspecialchars($post['email']), FILTER_VALIDATE_EMAIL)) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Formulaire incomplet ou invalide');
                    return $this->redirectToRoute('home');
                }

                $message = new Messages;
                $message->setName($post['name']);
                $message->setEmail($post['email']);
                $message->setPhone($post['phone']);
                $message->setSubject($post['subject']);
                $message->setMessage($post['message']);
                $message->setSentAt(new \DateTime('now'));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

                //
                $email = (new Email())
                    ->from($post['email'])
                    // ->to()
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject($post['subject'])
                    ->text($post['message']);
                // ->html('<p>See Twig integration for better HTML integration!</p>');

                // $mailer->send($email);


                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre nous avons bien reçu votre message. Nous vous répondrons dans les plus brefs délais.');

                return $this->redirectToRoute('home');
            }

            //Envoi d'un message utilisateur
            $this->addFlash('fail', 'Formulaire incomplet ou invalide');
            return $this->redirectToRoute('home');
        }

        $this->addFlash('fail', 'Méthode non-autorisée. Un problème est survenu.');
        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/qui-sommes-nous", name="home_people", methods={"GET"})
     */
    public function indexPeople(PeopleRepository $peopleRepository): Response
    {
        return $this->render('home/people.html.twig', [
            'people' => $peopleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/equipe-petits-eclaireurs", name="home_younglings", methods={"GET"})
     */
    public function indexYounglings(YounglingsRepository $younglingsRepository, PagesRepository $pagesRepository): Response
    {

        $page = $pagesRepository->findOneBy(['title' => 'Découvrez toute l\'équipe des petits éclaireurs']);

        return $this->render('home/younglings.html.twig', [
            'younglings' => $younglingsRepository->findAll(),
            'page' => $page
        ]);
    }

    /**
     * @Route("/faq", name="home_faq", methods={"GET"})
     */
    public function indexFAQ(FrequentlyAskedQuestionsRepository $faqRepository): Response
    {
        return $this->render('home/faq.html.twig', [
            'faqs' => $faqRepository->findAll(),
        ]);
    }

    /**
     * @Route("/page/{slug}", name="home_post", methods={"GET"})
     */
    public function showPost(Pages $page = null): Response
    {
        return $this->render('home/page.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/categorie/{slug}", name="home_category", requirements={"slug"=".+"}, methods={"GET"})
     */
    public function showCategory(string $slug = null, CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository): Response
    {

        if (isset($slug) && !empty($slug)) {

            //Récupération des slugs de chacune des catégories sélectionnées par l'utilisateur
            $slugs = explode('/', $slug);

            $topCategoryTitlesAndSlugs = [];


            foreach ($slugs as $categorySlug) {
                $category = $categoriesRepository->findOneBy(['slug' => $categorySlug]);

                //Si la catégorie n'existe pas, on redirige l'utilisateur vers l'acceuill avec un message
                if (!$category) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Catégorie sélectionnée inéxistante.');
                    return $this->redirectToRoute('home');
                }

                $topCategoryTitlesAndSlugs[] = [
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "categorySlug" => explode($categorySlug, $slug)[0].$categorySlug,
                ];

                //Autrement, on récolte les identifiants de tous les produits qui partagent l'ensemble des catégories sélectionnées par l'utilisateur
                if (empty($productIdsToVerifiy) || !isset($productIdsToVerifiy)) {
                    $productIdsToVerifiy = [];
                    foreach ($category->getProducts() as $product) {
                        $productIdsToVerifiy[] = $product->getId();
                    }
                } elseif (!empty($productIdsToVerifiy) && isset($productIdsToVerifiy)) {
                    $productIdsToCompare = [];
                    foreach ($category->getProducts() as $product) {
                        $productIdsToCompare[] = $product->getId();
                    }
                    $productIdsToVerifiy = array_intersect($productIdsToVerifiy, $productIdsToCompare);
                }
            }

            $products = $productsRepository->findBy(['id' => $productIdsToVerifiy]);


            $subCategorieIds = [];
            foreach ($products as $product) {
                foreach ($product->getCategory() as $subCategory) {
                    if ($subCategory != $category) {
                        $subCategorieIds[] = $subCategory->getId();
                    }
                }
            }



            $subCategoryTitlesAndSlugs = [];
            foreach (array_count_values($subCategorieIds) as $id => $nbrOccurences) {

                $subCategoryTitleAndSlug = [
                    "categoryId" => $id,
                    "categoryTitle" => $categoriesRepository->find($id)->getTitle(),
                    "categorySlug" => $categoriesRepository->find($id)->getSlug()
                ];

                if (!in_array($subCategoryTitleAndSlug, $topCategoryTitlesAndSlugs)) {
                    $subCategoryTitleAndSlug["categorySlug"] = $slug . '/' . $categoriesRepository->find($id)->getSlug();
                    $subCategoryTitleAndSlug["nbrOfOccurences"] = $nbrOccurences;
                    $subCategoryTitlesAndSlugs[] = $subCategoryTitleAndSlug;
                }
                unset( $subCategoryTitleAndSlug);
            }


            return $this->render('home/categories.html.twig', [
                'category' => $category,
                'products' => $products,
                'topCategoryTitlesAndSlugs' => $topCategoryTitlesAndSlugs,
                'subCategoryTitlesAndSlugs' => $subCategoryTitlesAndSlugs
            ]);
        }

        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Catégorie sélectionnée inéxistante.');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/sortie/{slugCategory}/{slugProduct}", name="home_product", requirements={"slugCategory"=".+"}, methods={"GET","POST"})
     */
    public function showProduct(string $slugCategory, string $slugProduct, CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, AttributesRepository $attributesRepository, Request $request): Response
    {

        if (isset($slugCategory) && !empty($slugCategory) && 
            isset($slugProduct) && !empty($slugProduct)
        ) {
            $product = $productsRepository->findOneBy(['slug' => $slugProduct]);

            $categorySlugs = explode('/',$slugCategory);
            $category = $categoriesRepository->findOneBy(['slug' => end($categorySlugs)]);

            $topCategoryTitlesAndSlugs = [];

            foreach ($categorySlugs as $categorySlug) {
                $category = $categoriesRepository->findOneBy(['slug' => $categorySlug]);

                //Si la catégorie n'existe pas, on redirige l'utilisateur vers l'acceuill avec un message
                if (!$category) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Catégorie sélectionnée inéxistante.');
                    return $this->redirectToRoute('home');
                }

                $topCategoryTitlesAndSlugs[] = [
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "categorySlug" => explode($categorySlug, $slugCategory)[0].$categorySlug,
                ];
            }



            $attributes = [];
            foreach ($product->getAttribute() as $value) {
                $attributes[] = $attributesRepository->find($value);
            }

            $moderatedComments = [];
            foreach ($product->getComments() as $comment) {
                if ($comment->getIsModerated() === true) {
                    $moderatedComments[] = $comment;
                }
            }

            $comment = new Comments();
            $form1 = $this->createForm(CommentsType::class, $comment);
            $form1->handleRequest($request);

            $message = new Messages();
            $form2 = $this->createForm(MessagesType::class, $message);
            $form2->handleRequest($request);

            if ($form1->isSubmitted() && $form1->isValid()) {

                $comment->setProduct($product);
                $comment->setCreatedAt(new \DateTime('now'));
                $comment->setIsModerated(0);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre commentaire pour la sortie "' . $product->getTitle() . '" a été enregistré et est en attente de modération.');

                return $this->redirectToRoute('home');
            }

            if ($form2->isSubmitted() && $form2->isValid()) {

                $message->setSubject($product->getTitle());
                $message->setProduct($product);
                $message->setSentAt(new \DateTime('now'));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre nous avons bien reçu votre message pour la sortie "' . $product->getTitle() . '". Nous vous répondrons dans les plus brefs délais.');

                return $this->redirectToRoute('home');
            }

            return $this->render('home/products.html.twig', [
                'product' => $product,
                'category' => $category,
                'topCategoryTitlesAndSlugs'=>$topCategoryTitlesAndSlugs,
                'attributes' => $attributes,
                'moderatedComments' => $moderatedComments,
                'form' => $form1->createView(),
                'form2' => $form2->createView(),
            ]);
        }

        return $this->redirectToRoute('home');
    }



    /**
     * Vérifie que les champs du formulaire contiennent tous au moins 5 caractères
     *
     * @param $post
     * @return bool|string
     */
    private function isFormValid($post)
    {
        //On vérifie si les données à stocker comportent tous au moins 5 caractères. Si ce n'est pas le cas, on renvoie une chaine de carctères.
        $fields = ['name', 'email', 'phone', 'subject', 'message'];
        foreach ($fields as $field) {
            if (iconv_strlen(htmlspecialchars($post[$field])) < 5) {
                return false;
            }
        }

        //Pot de miel : on vérifie si la variable-pot de miel est bien dans le tableau associatif est si elle est vide. Si ce n'est pas le cas, on renvoie une chaine de carctères
        if (
            !isset($post['nosiar']) || !empty(htmlspecialchars($post['nosiar']))
        ) {
            return false;
        }
        return true;
    }

    /**
     * Vérifie si les champs d'un formulaire sont remplis
     *
     * @param $post array
     * @return bool|string
     */
    private function isFormFilled($post)
    {
        //On vérifie si les données à stocker sont vides, si c'est le cas, on renvoie une chaine de carctères
        $fields = ['name', 'email', 'phone', 'subject', 'message'];
        foreach ($fields as $field) {
            if (
                !isset($post[$field]) || empty(htmlspecialchars($post[$field]))
            ) {
                return false;
            }
        }
        //Pot de miel : on vérifie si la variable-pot de miel est bien dans le tableau associatif est si elle est vide. Si ce n'est pas le cas, on renvoie une chaine de carctères
        if (
            !isset($post['nosiar']) || !empty(htmlspecialchars($post['nosiar']))
        ) {
            return false;
        }
        return true;
    }
}
