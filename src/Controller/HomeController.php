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
use App\Repository\LinksRepository;
use App\Repository\PagesRepository;
use App\Repository\PeopleRepository;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use App\Repository\YounglingsRepository;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/accueil", name="home")
     */
    public function index(LinksRepository $linksRepository, PagesRepository $pagesRepository, UsersRepository $usersRepository)
    {

        if ($pagesRepository->findBy(['title' => 'Accueil'])) {

            $page = $pagesRepository->findBy(['title' => 'Accueil'])[0];
        } else {
            //Instanciation entité Pages
            $page = new Pages;

            $page->setTitle('Accueil');
            $page->setMetaTagTitle('Contenu à éditer');
            $page->setContent('Contenu à éditer');
            $page->setMetaTagDescription('Contenu à éditer');

            $keywords = ["Contenu à éditer"];
            $page->setKeyWords($keywords);
            $page->setMetaTagKeywords($keywords);
            $page->setCreatedAt(new \DateTime('now'));
            $page->setUpdatedAt(new \DateTime('now'));

            $page->setUser($usersRepository->findAll()[0]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $entityManager->flush();
        }

        return $this->render('home/index.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/home/{slug}", name="foreign")
     */
    public function foreignIndex(string $slug, PagesRepository $pagesRepository, UsersRepository $usersRepository)
    {

        $title = ucwords(str_replace('-', ' ', $slug));

        if ($pagesRepository->findBy(['title' => $title])) {

            $page = $pagesRepository->findBy(['title' => $title])[0];
        } else {
            //Instanciation entité Pages
            $page = new Pages;

            $page->setTitle($title);
            $page->setMetaTagTitle('Contenu à éditer');
            $page->setContent('Contenu à éditer');
            $page->setMetaTagDescription('Contenu à éditer');

            $keywords = ["Contenu à éditer"];
            $page->setKeyWords($keywords);
            $page->setMetaTagKeywords($keywords);
            $page->setCreatedAt(new \DateTime('now'));
            $page->setUpdatedAt(new \DateTime('now'));

            $page->setUser($usersRepository->findAll()[0]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $entityManager->flush();
        }

        return $this->render('home/index.html.twig', [
            'page' => $page,
        ]);
    }


    /**
     * @Route("/send/email", name="send_email", methods={"GET","POST"})
     */
    public function processEmailForm(Request $request, \Swift_Mailer $mailer, UsersRepository $usersRepository)
    {

        if ($request->getMethod() == 'POST') {

            $post = $request->request->All();

            if ($this->isFormFilled($post) && $this->isFormValid($post)) {

                //On vérifie si l'adresse mail de l'utilisateur est valide 
                if (!filter_var(htmlspecialchars($post['email']), FILTER_VALIDATE_EMAIL)) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Formulaire incomplet ou invalide');
                    return $this->redirectToRoute('home');
                }

                //Instanciation Messages pour enregistrement en bdd du message envoyé par l'utilisateur
                $message = new Messages;
                $message->setName($post['name']);
                $message->setEmail($post['email']);
                $message->setPhone($post['phone']);
                $message->setSubject($post['subject']);
                $message->setMessage($post['message']);
                $message->setSentAt(new \DateTime('now'));

                //Expedition du message
                $this->sendEmails($post, $mailer, $usersRepository);

                //Enregistrement en bdd du message
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

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
     * @Route("/***REMOVED***-thematiques", name="home_theme", methods={"GET"})
     */
    public function showThemes(CategoriesRepository $categoriesRepository): Response
    {
        $themeSlugs = [
            "art",
            "comment-ca-marche",
            "raconte-moi-histoire",
            "derriere-le-rideau",
            "quand-je-serai-grand",
            "moi-qui-fait",
            "city-break",
            "initiation-a-l-art-pour-adultes",
            "les-grandes-expositions-en-famille"
        ];

        $categories = [];
        foreach ($themeSlugs as $themeSlug) {
            if ($categoriesRepository->findOneBy(['slug' => $themeSlug])) {
                $categories[] = $categoriesRepository->findOneBy(['slug' => $themeSlug]);
            }
        }

        // dd($categories);

        return $this->render('home/themes.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/toutes-les-***REMOVED***", name="home_all_products", methods={"GET"})
     */
    public function showAllProducts(ProductsRepository $productsRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $products = $paginator->paginate(
            $productsRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'élément par page
            10
        );

        return $this->render('home/allProducts.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/categorie/{slug}", name="home_category", requirements={"slug"=".+"}, methods={"GET"})
     */
    public function showCategory(string $slug = null, CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        //Si le paramètre "slug" passé en barre d'url n'est pas défini, on renvoie l'utilisateur vers l'accueil
        if (isset($slug) && !empty($slug)) {

            //---------------Traitement des catégories ascendantes sélectionnées par l'utilisateur pour filtrage des produits par catégories--------------------

            //Récupération des slugs de chacune des catégories sélectionnées
            $slugs = explode('/', $slug);

            //Dans ce tableau, on stocke les titres, slug et identifiants des catégories sélectionnées
            $topCategoryTitlesAndSlugs = [];
            foreach ($slugs as $categorySlug) {
                //Pour chacun des slugs sélectionnés, on va chercher la catégorie correspondante 
                $category = $categoriesRepository->findOneBy(['slug' => $categorySlug]);

                //Si la catégorie n'est pas en bdd, on redirige l'utilisateur vers l'accueil avec un message
                if (!$category) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Catégorie sélectionnée inéxistante.');
                    return $this->redirectToRoute('home');
                }

                //on stocke le titre, le slug et l'identifiant de la catégorie
                //Ce tableau servira à affiché les catégories précédemment sélectionnées en haut de la page.
                //Par exemple :  "Accueil>Visites guidées en France>Bourgogne>..." 
                $topCategoryTitlesAndSlugs[] = [
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    //Dans ce paramêtre, on insère les slugs des catégories précédemment séléctionnées tout au long de la navigation via la zone de filtre par catégorie dans le template : les catégories précédemment sélectionnées sont mémorisés dans categorySlug.
                    "categorySlug" => explode($categorySlug, $slug)[0] . $categorySlug,
                ];

                //on récolte les identifiants de tous les produits qui partagent l'ensemble des catégories sélectionnées par l'utilisateur.
                //On procède en cascade : d'abord stockage dans un tableau (productIdsToVerifiy) des identifiants produit, utilisable après itération de la boucle
                if (empty($productIdsToVerifiy) || !isset($productIdsToVerifiy)) {
                    $productIdsToVerifiy = [];
                    foreach ($category->getProducts() as $product) {
                        $productIdsToVerifiy[] = $product->getId();
                    }
                } elseif (!empty($productIdsToVerifiy) && isset($productIdsToVerifiy)) {
                    //On utilise le tableau productIdsToVerifiy ici. On le compare avec un 2eme tableau(productIdsToCompare) qui contient les identifiants produit de la catégorie suivante dans l'arborescence des catégories
                    $productIdsToCompare = [];
                    foreach ($category->getProducts() as $product) {
                        $productIdsToCompare[] = $product->getId();
                    }
                    //On ne récupère que l'intersection de ces 2 tableaux, que l'on charge dans productIdsToVerifiy
                    $productIdsToVerifiy = array_intersect($productIdsToVerifiy, $productIdsToCompare);
                    //On procède de la sorte jusqu'à ce que toutes les catégories sélectionnées par l'utilisateur soient traitées
                }
            }
            //Il en résulte un filtrage des produits qui appartiennent tous aux catégories sélectionnées par l'utilisateur. Ce sont les produits qui seront affichés dans le template
            $products = $productsRepository->findBy(['id' => $productIdsToVerifiy]);


            //-------------Traitement des catégories descendantes pour sélection ultérieure par l'utilisateur--------------------

            //Pour chacun des produits filtrés précédemment, on récupère les identifiants des catégories qui leur sont associées et les stocke dans un tableau (subCategorieIds)
            $subCategorieIds = [];
            foreach ($products as $product) {
                foreach ($product->getCategory() as $subCategory) {
                    if ($subCategory != $category) {
                        $subCategorieIds[] = $subCategory->getId();
                    }
                }
            }

            //Ce tableau servira à donner la possibilité à l'utilisateur de sélectionner d'autres catégories pour un filtrage plus fin des produits 
            $subCategoryTitlesAndSlugs = [];
            //On parcourt le tableau subCategorieIds sans les doublons
            foreach (array_count_values($subCategorieIds) as $id => $nbrOccurences) {

                //Ces informations seront stockées dans le tableau subCategoryTitlesAndSlugs si...
                $subCategoryTitleAndSlug = [
                    "categoryId" => $id,
                    "categoryTitle" => $categoriesRepository->find($id)->getTitle(),
                    "categorySlug" => $categoriesRepository->find($id)->getSlug()
                ];

                //... elles ne sont pas présentes dans le tableau (topCategoryTitlesAndSlugs) des catégories déjà sélectionnées par l'utilisateur
                if (!in_array($subCategoryTitleAndSlug, $topCategoryTitlesAndSlugs)) {
                    //Dans ce paramêtre, on insère les slugs des catégories précédemment séléctionnées tout au long de la navigation via la zone de filtre par catégorie dans le template : les catégories précédemment sélectionnées sont mémorisés dans categorySlug.
                    $subCategoryTitleAndSlug["categorySlug"] = $slug . '/' . $categoriesRepository->find($id)->getSlug();
                    //On stocke le nombre de produits qui seront affichés après filtrage
                    $subCategoryTitleAndSlug["nbrOfOccurences"] = $nbrOccurences;
                    $subCategoryTitlesAndSlugs[] = $subCategoryTitleAndSlug;
                }
                unset($subCategoryTitleAndSlug);
            }


            // Pagination
            $products = $paginator->paginate(
                $products,
                //Le numero de la page, si aucun numero, on force la page 1
                $request->query->getInt('page', 1),
                //Nombre d'élément par page
                10
            );


            return $this->render('home/category.html.twig', [
                'category' => $category,
                'products' => $products,
                'topCategoryTitlesAndSlugs' => $topCategoryTitlesAndSlugs,
                'subCategoryTitlesAndSlugs' => $subCategoryTitlesAndSlugs
            ]);
        }

        //Si le paramètre "slug" passé en barre d'url n'est pas défini, on renvoie l'utilisateur vers l'accueil
        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Catégorie sélectionnée inéxistante.');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/sortie/{slugCategory}/{slugProduct}", name="home_product", requirements={"slugCategory"=".+"}, methods={"GET","POST"})
     */
    public function showProduct(string $slugCategory, string $slugProduct, CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, AttributesRepository $attributesRepository, Request $request, \Swift_Mailer $mailer, UsersRepository $usersRepository): Response
    {

        if (
            isset($slugCategory) && !empty($slugCategory) &&
            isset($slugProduct) && !empty($slugProduct)
        ) {
            $product = $productsRepository->findOneBy(['slug' => $slugProduct]);

            if ($slugCategory === "toutes-les-***REMOVED***") {

                $topCategoryTitlesAndSlugs = [];
                $category = null;
                $formAllProducts = true;
            } else {

                $formAllProducts = false;

                $categorySlugs = explode('/', $slugCategory);
                $category = $categoriesRepository->findOneBy(['slug' => end($categorySlugs)]);

                $topCategoryTitlesAndSlugs = [];

                foreach ($categorySlugs as $categorySlug) {
                    $category = $categoriesRepository->findOneBy(['slug' => $categorySlug]);

                    //Si la catégorie n'existe pas, on redirige l'utilisateur vers l'accueil avec un message
                    if (!$category) {
                        //Envoi d'un message utilisateur
                        $this->addFlash('fail', 'Catégorie sélectionnée inéxistante.');
                        return $this->redirectToRoute('home');
                    }

                    $topCategoryTitlesAndSlugs[] = [
                        "categoryId" => $category->getId(),
                        "categoryTitle" => $category->getTitle(),
                        "categorySlug" => explode($categorySlug, $slugCategory)[0] . $categorySlug,
                    ];
                }
            }

            //Récupération des attributs
            $attributes = [];
            foreach ($product->getAttribute() as $value) {
                $attributes[] = $attributesRepository->find($value);
            }

            //Récupération des commentaires modérés
            $moderatedComments = [];
            foreach ($product->getComments() as $comment) {
                if ($comment->getIsModerated() === true) {
                    $moderatedComments[] = $comment;
                }
            }

            //Instanciation de Comments, création formulaire commentaire 
            $comment = new Comments();
            $formComment = $this->createForm(CommentsType::class, $comment);
            $formComment->handleRequest($request);

            //Instanciation de Messages, création formulaire de contact 
            $message = new Messages();
            $formContact = $this->createForm(MessagesType::class, $message);
            $formContact->handleRequest($request);

            //Soumission formulaire commentaire
            if ($formComment->isSubmitted() && $formComment->isValid()) {

                $comment->setProduct($product);
                $comment->setCreatedAt(new \DateTime('now'));
                $comment->setIsModerated(0);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre commentaire pour la sortie "' . $product->getTitle() . '" a été enregistré et est en attente de modération.');

                return $this->redirectToRoute('home_product', [
                    'slugCategory' => $slugCategory,
                    'slugProduct' => $slugProduct
                ]);
            }

            //Soumission formulaire réservation/contact
            if ($formContact->isSubmitted() && $formContact->isValid()) {

                $message->setSubject($product->getTitle());
                $message->setProduct($product);
                $message->setSentAt(new \DateTime('now'));

                //Préparation des données pour l'expedition d'email
                $post['name'] = $message->getName();
                $post['subject'] = $product->getTitle();
                $post['email'] = $message->getEmail();
                $post['phone'] = $message->getPhone();
                $post['message'] = $message->getMessage();
                $post['wished_date'] = $message->getWishedDate();
                //Expedition du message
                $this->sendEmails($post, $mailer, $usersRepository);


                //Enregistrement du message en bdd
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();




                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre nous avons bien reçu votre message pour la sortie "' . $product->getTitle() . '". Nous vous répondrons dans les plus brefs délais.');

                return $this->redirectToRoute('home_product', [
                    'slugCategory' => $slugCategory,
                    'slugProduct' => $slugProduct
                ]);
            }

            return $this->render('home/product.html.twig', [
                'product' => $product,
                'category' => $category,
                'formAllProducts' => $formAllProducts,
                'topCategoryTitlesAndSlugs' => $topCategoryTitlesAndSlugs,
                'attributes' => $attributes,
                'moderatedComments' => $moderatedComments,
                'form' => $formComment->createView(),
                'formContact' => $formContact->createView(),
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


    private function sendEmails(array $post, \Swift_Mailer $mailer, UsersRepository $usersRepository )
    {

        //On récupère les emails de tous les administrateurs du site
        $admin_emails = [];
        foreach ($usersRepository->findAll() as $user) {
            $role =  $user->getRoles();
            if (in_array("ROLE_ADMIN", $role)) {
                $admin_emails[] = $user->getEmail();
            }
        }

        $email = (new \Swift_Message('Vous avez reçu un nouvel email de '.$post['name']))
            //Objet du message
            ->setSubject($post['subject'])
            // On attribue l'expéditeur
            ->setFrom($post['email'])
            // On attribue le destinataire
            ->setTo($admin_emails)
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'emails/emailBody.html.twig',
                    compact('post')
                ),
                'text/html'
            );

        $mailer->send($email);
    }
}
