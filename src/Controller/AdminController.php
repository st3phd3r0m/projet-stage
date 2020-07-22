<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\CommentsRepository;
use App\Repository\MessagesRepository;
use App\Repository\PagesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(ProductsRepository $productsRepository, CategoriesRepository $categoriesRepository, CommentsRepository $commentsRepository, MessagesRepository $messagesRepository, PagesRepository $pagesRepository)
    {


        $nbrProducts = count($productsRepository->findAll());
        $nbrCategories = count($categoriesRepository->findAll());
        $nbrPages = count($pagesRepository->findAll());
        $nbrComments = count($commentsRepository->findAll());
        $nbrMessages = count($messagesRepository->findAll());

        return $this->render('admin/index.html.twig', [
            'nbrProducts' => $nbrProducts,
            'nbrCategories' => $nbrCategories,
            'nbrPages' => $nbrPages,
            'nbrComments'=>$nbrComments,
            'nbrMessages'=>$nbrMessages
        ]);
    }
}
