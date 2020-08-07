<?php

namespace App\Controller;

use App\Twig\AppExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


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
}
