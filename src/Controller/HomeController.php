<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\PeopleRepository;
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

            if( $this->isFormFilled($post) && $this->isFormValid($post)  ){

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
                ->to('stephane.derom@gmail.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($post['subject'])
                ->text($post['message']);
                // ->html('<p>See Twig integration for better HTML integration!</p>');
    
                $mailer->send($email);

    
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
     * Vérifie que les champs du formulaire contiennent tous au moins 5 caractères
     *
     * @param $post
     * @return bool|string
     */
    private function isFormValid($post)
    {
        //On vérifie si les données à stocker comportent tous au moins 5 caractères. Si ce n'est pas le cas, on renvoie une chaine de carctères.
        $fields = ['name', 'email', 'phone' ,'subject', 'message'];
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
