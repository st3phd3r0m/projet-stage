<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Messages;
use App\Entity\Pages;
use App\Form\CommentsType;
use App\Form\MessagesType;
use App\Repository\AttributesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\FrequentlyAskedQuestionsRepository;
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
    public function index(PagesRepository $pagesRepository)
    {

        $this->forward('App\Controller\PagesController::newFirmPage', [
            'slug'  => 'accueil'
        ]);

        $page = $pagesRepository->findOneBy(['slug' => 'accueil']);

        return $this->render('home/index.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/home/{lang}/{slug}", name="foreign")
     */
    public function foreignIndex(string $slug, string $lang, PagesRepository $pagesRepository)
    {

        $this->forward('App\Controller\PagesController::newFirmPage', [
            'slug'  => $slug,
            'lang' => $lang
        ]);

        $page = $pagesRepository->findOneBy(['slug' => $slug]);

        return $this->render('home/index.html.twig', [
            'page' => $page
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

                //On v??rifie si l'adresse mail de l'utilisateur est valide 
                if (!filter_var(htmlspecialchars($post['email']), FILTER_VALIDATE_EMAIL)) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Formulaire incomplet ou invalide');
                    return $this->redirectToRoute('home');
                }

                //Instanciation Messages pour enregistrement en bdd du message envoy?? par l'utilisateur
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
                $this->addFlash('success', 'Nous avons bien re??u votre message. Nous vous r??pondrons dans les plus brefs d??lais.');

                return $this->redirectToRoute('home');
            }

            //Envoi d'un message utilisateur
            $this->addFlash('fail', 'Formulaire incomplet ou invalide');
            return $this->redirectToRoute('home');
        }

        $this->addFlash('fail', 'M??thode non-autoris??e. Un probl??me est survenu.');
        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/qui-sommes-nous", name="home_people", methods={"GET"})
     */
    public function indexPeople(PeopleRepository $peopleRepository, PagesRepository $pagesRepository): Response
    {
        $page = $pagesRepository->findOneBy(['slug'=>'qui-sommes-nous']);

        return $this->render('home/people.html.twig', [
            'people' => $peopleRepository->findAll(),
            'page' => $page
        ]);
    }

    /**
     * @Route("/equipe-beta-testers", name="home_younglings", methods={"GET"})
     */
    public function indexYounglings(YounglingsRepository $younglingsRepository, PagesRepository $pagesRepository): Response
    {
        $page = $pagesRepository->findOneBy(['slug'=>'equipe-beta-testers']);

        return $this->render('home/younglings.html.twig', [
            'younglings' => $younglingsRepository->findAll(),
            'page' => $page
        ]);
    }

    /**
     * @Route("/foire-aux-questions", name="home_faq", methods={"GET"})
     */
    public function indexFAQ(FrequentlyAskedQuestionsRepository $faqRepository, PagesRepository $pagesRepository): Response
    {
        $page = $pagesRepository->findOneBy(['slug'=>'foire-aux-questions']);

        return $this->render('home/faq.html.twig', [
            'faqs' => $faqRepository->findAll(),
            'page' => $page
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
     * @Route("/produits-thematiques", name="home_theme", methods={"GET"})
     */
    public function showThemes(CategoriesRepository $categoriesRepository, PagesRepository $pagesRepository): Response
    {
        $this->forward('App\Controller\PagesController::newFirmPage', [
            'slug'  => 'produits-thematiques'
        ]);

        $page = $pagesRepository->findOneBy(['slug' => 'produits-thematiques']);

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

        return $this->render('home/themes.html.twig', [
            'categories' => $categories,
            'page' => $page
        ]);
    }

    /**
     * @Route("/tous-les-produits", name="home_all_products", methods={"GET"})
     */
    public function showAllProducts(ProductsRepository $productsRepository, PaginatorInterface $paginator, Request $request, PagesRepository $pagesRepository): Response
    {

        $page = $pagesRepository->findOneBy(['slug'=>'tous-les-produits']);

        $products = $paginator->paginate(
            $productsRepository->findAll(),
            //Le numero de la page, si aucun numero, on force la page 1
            $request->query->getInt('page', 1),
            //Nombre d'??l??ment par page
            10
        );

        return $this->render('home/allProducts.html.twig', [
            'products' => $products,
            'page' => $page
        ]);
    }

    /**
     * @Route("/categorie/{slug}", name="home_category", requirements={"slug"=".+"}, methods={"GET"})
     */
    public function showCategory(string $slug = null, CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        //Si le param??tre "slug" pass?? en barre d'url n'est pas d??fini, on renvoie l'utilisateur vers l'accueil
        if (isset($slug) && !empty($slug)) {

            //---------------Traitement des cat??gories ascendantes s??lectionn??es par l'utilisateur pour filtrage des produits par cat??gories--------------------

            //R??cup??ration des slugs de chacune des cat??gories s??lectionn??es
            $slugs = explode('/', $slug);

            //Dans ce tableau, on stocke les titres, slug et identifiants des cat??gories s??lectionn??es
            $topCategoryTitlesAndSlugs = [];
            foreach ($slugs as $categorySlug) {
                //Pour chacun des slugs s??lectionn??s, on va chercher la cat??gorie correspondante 
                $category = $categoriesRepository->findOneBy(['slug' => $categorySlug]);

                //Si la cat??gorie n'est pas en bdd, on redirige l'utilisateur vers l'accueil avec un message
                if (!$category) {
                    //Envoi d'un message utilisateur
                    $this->addFlash('fail', 'Cat??gorie s??lectionn??e in??xistante.');
                    return $this->redirectToRoute('home');
                }

                //on stocke le titre, le slug et l'identifiant de la cat??gorie
                //Ce tableau servira ?? affich?? les cat??gories pr??c??demment s??lectionn??es en haut de la page.
                //Par exemple :  "Accueil>Visites guid??es en France>Bourgogne>..." 
                $topCategoryTitlesAndSlugs[] = [
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    //Dans ce param??tre, on ins??re les slugs des cat??gories pr??c??demment s??l??ctionn??es tout au long de la navigation via la zone de filtre par cat??gorie dans le template : les cat??gories pr??c??demment s??lectionn??es sont m??moris??s dans categorySlug.
                    "categorySlug" => explode($categorySlug, $slug)[0] . $categorySlug,
                ];

                //on r??colte les identifiants de tous les produits qui partagent l'ensemble des cat??gories s??lectionn??es par l'utilisateur.
                //On proc??de en cascade : d'abord stockage dans un tableau (productIdsToVerifiy) des identifiants produit, utilisable apr??s it??ration de la boucle
                if (empty($productIdsToVerifiy) || !isset($productIdsToVerifiy)) {
                    $productIdsToVerifiy = [];
                    foreach ($category->getProducts() as $product) {
                        $productIdsToVerifiy[] = $product->getId();
                    }
                } elseif (!empty($productIdsToVerifiy) && isset($productIdsToVerifiy)) {
                    //On utilise le tableau productIdsToVerifiy ici. On le compare avec un 2eme tableau(productIdsToCompare) qui contient les identifiants produit de la cat??gorie suivante dans l'arborescence des cat??gories
                    $productIdsToCompare = [];
                    foreach ($category->getProducts() as $product) {
                        $productIdsToCompare[] = $product->getId();
                    }
                    //On ne r??cup??re que l'intersection de ces 2 tableaux, que l'on charge dans productIdsToVerifiy
                    $productIdsToVerifiy = array_intersect($productIdsToVerifiy, $productIdsToCompare);
                    //On proc??de de la sorte jusqu'?? ce que toutes les cat??gories s??lectionn??es par l'utilisateur soient trait??es
                }
            }
            //Il en r??sulte un filtrage des produits qui appartiennent tous aux cat??gories s??lectionn??es par l'utilisateur. Ce sont les produits qui seront affich??s dans le template
            $products = $productsRepository->findBy(['id' => $productIdsToVerifiy]);


            //-------------Traitement des cat??gories descendantes pour s??lection ult??rieure par l'utilisateur--------------------

            //Pour chacun des produits filtr??s pr??c??demment, on r??cup??re les identifiants des cat??gories qui leur sont associ??es et les stocke dans un tableau (subCategorieIds)
            $subCategorieIds = [];
            foreach ($products as $product) {
                foreach ($product->getCategory() as $subCategory) {
                    if ($subCategory != $category) {
                        $subCategorieIds[] = $subCategory->getId();
                    }
                }
            }

            //Ce tableau servira ?? donner la possibilit?? ?? l'utilisateur de s??lectionner d'autres cat??gories pour un filtrage plus fin des produits 
            $subCategoryTitlesAndSlugs = [];
            //On parcourt le tableau subCategorieIds sans les doublons
            foreach (array_count_values($subCategorieIds) as $id => $nbrOccurences) {

                //Ces informations seront stock??es dans le tableau subCategoryTitlesAndSlugs si...
                $subCategoryTitleAndSlug = [
                    "categoryId" => $id,
                    "categoryTitle" => $categoriesRepository->find($id)->getTitle(),
                    "categorySlug" => $categoriesRepository->find($id)->getSlug()
                ];

                //... elles ne sont pas pr??sentes dans le tableau (topCategoryTitlesAndSlugs) des cat??gories d??j?? s??lectionn??es par l'utilisateur
                if (!in_array($subCategoryTitleAndSlug, $topCategoryTitlesAndSlugs)) {
                    //Dans ce param??tre, on ins??re les slugs des cat??gories pr??c??demment s??l??ctionn??es tout au long de la navigation via la zone de filtre par cat??gorie dans le template : les cat??gories pr??c??demment s??lectionn??es sont m??moris??s dans categorySlug.
                    $subCategoryTitleAndSlug["categorySlug"] = $slug . '/' . $categoriesRepository->find($id)->getSlug();
                    //On stocke le nombre de produits qui seront affich??s apr??s filtrage
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
                //Nombre d'??l??ment par page
                10
            );


            return $this->render('home/category.html.twig', [
                'category' => $category,
                'products' => $products,
                'topCategoryTitlesAndSlugs' => $topCategoryTitlesAndSlugs,
                'subCategoryTitlesAndSlugs' => $subCategoryTitlesAndSlugs
            ]);
        }

        //Si le param??tre "slug" pass?? en barre d'url n'est pas d??fini, on renvoie l'utilisateur vers l'accueil
        //Envoi d'un message utilisateur
        $this->addFlash('fail', 'Cat??gorie s??lectionn??e in??xistante.');
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

            if ($slugCategory === "tous-les-produits") {

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

                    //Si la cat??gorie n'existe pas, on redirige l'utilisateur vers l'accueil avec un message
                    if (!$category) {
                        //Envoi d'un message utilisateur
                        $this->addFlash('fail', 'Cat??gorie s??lectionn??e in??xistante.');
                        return $this->redirectToRoute('home');
                    }

                    $topCategoryTitlesAndSlugs[] = [
                        "categoryId" => $category->getId(),
                        "categoryTitle" => $category->getTitle(),
                        "categorySlug" => explode($categorySlug, $slugCategory)[0] . $categorySlug,
                    ];
                }
            }

            //R??cup??ration des attributs
            $attributes = [];
            foreach ($product->getAttribute() as $value) {
                $attributes[] = $attributesRepository->find($value);
            }

            //R??cup??ration des commentaires mod??r??s
            $moderatedComments = [];
            foreach ($product->getComments() as $comment) {
                if ($comment->getIsModerated() === true) {
                    $moderatedComments[] = $comment;
                }
            }

            //Instanciation de Comments, cr??ation formulaire commentaire 
            $comment = new Comments();
            $formComment = $this->createForm(CommentsType::class, $comment);
            $formComment->handleRequest($request);

            //Instanciation de Messages, cr??ation formulaire de contact 
            $message = new Messages();
            $formContact = $this->createForm(MessagesType::class, $message);
            $formContact->handleRequest($request);

            //Soumission formulaire commentaire
            if ($formComment->isSubmitted() && $formComment->isValid()) {

                if($formComment->getData()->getMark() === null){
                    $comment->setMark(0);
                }

                $comment->setProduct($product);
                $comment->setCreatedAt(new \DateTime('now'));
                $comment->setIsModerated(0);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                //Envoi d'un message utilisateur
                $this->addFlash('success', 'Votre commentaire pour la sortie "' . $product->getTitle() . '" a ??t?? enregistr?? et est en attente de mod??ration.');

                return $this->redirectToRoute('home_product', [
                    'slugCategory' => $slugCategory,
                    'slugProduct' => $slugProduct
                ]);
            }

            //Soumission formulaire r??servation/contact
            if ($formContact->isSubmitted() && $formContact->isValid()) {

                $message->setSubject($product->getTitle());
                $message->setProduct($product);
                $message->setSentAt(new \DateTime('now'));

                //Pr??paration des donn??es pour l'expedition d'email
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
                $this->addFlash('success', 'Nous avons bien re??u votre message pour la sortie "' . $product->getTitle() . '". Nous vous r??pondrons dans les plus brefs d??lais.');

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
     * V??rifie que les champs du formulaire de contact contiennent tous au moins 5 caract??res
     * @param $post
     * @return bool|string
     */
    private function isFormValid($post)
    {
        //On v??rifie si les donn??es ?? stocker comportent tous au moins 5 caract??res.
        $fields = ['name', 'email', 'phone', 'subject', 'message'];
        foreach ($fields as $field) {
            if (iconv_strlen(htmlspecialchars($post[$field])) < 5) {
                return false;
            }
        }

        //Pot de miel : on v??rifie si la variable-pot de miel est bien dans le tableau associatif et si elle est vide.
        if (
            !isset($post['nosiar']) || !empty(htmlspecialchars($post['nosiar']))
        ) {
            return false;
        }
        return true;
    }

    /**
     * V??rifie si les champs d'un formulaire sont remplis
     * @param $post array
     * @return bool|string
     */
    private function isFormFilled($post)
    {
        //On v??rifie si les donn??es ?? stocker sont vides.
        $fields = ['name', 'email', 'phone', 'subject', 'message'];
        foreach ($fields as $field) {
            if (
                !isset($post[$field]) || empty(htmlspecialchars($post[$field]))
            ) {
                return false;
            }
        }
        //Pot de miel : on v??rifie si la variable-pot de miel est bien dans le tableau associatif et si elle est vide.
        if (
            !isset($post['nosiar']) || !empty(htmlspecialchars($post['nosiar']))
        ) {
            return false;
        }
        return true;
    }


    private function sendEmails(array $post, \Swift_Mailer $mailer, UsersRepository $usersRepository )
    {

        //On r??cup??re les emails de tous les administrateurs du site
        $admin_emails = [];
        foreach ($usersRepository->findAll() as $user) {
            $role =  $user->getRoles();
            if (in_array("ROLE_ADMIN", $role)) {
                $admin_emails[] = $user->getEmail();
            }
        }

        $email = (new \Swift_Message('Vous avez re??u un nouvel email de '.$post['name']))
            //Objet du message
            ->setSubject($post['subject'])
            // On attribue l'exp??diteur
            ->setFrom($post['email'])
            // On attribue le destinataire
            ->setTo($admin_emails)
            // On cr??e le texte avec la vue
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
