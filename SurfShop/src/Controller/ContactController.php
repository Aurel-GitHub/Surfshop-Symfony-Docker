<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{

    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais.');
            // $mail = new Mail();
            // $mon_mail = 'aurelien.landouer@hotmail.fr';
            // $mail_site = 'surfshop64@gmail.com';
            // $content = '<strong>Vous avez reçu un message depuis le site SurfShop 40/64 : </strong><br>'.$form->get('description')->getData(). '<br><strong>de la part de : </strong><br>'
            // .$form->get('prenom')->getData().' '.$form->get('nom')->getData().' '.$form->get('email')->getData();
            // $mail->send($mon_mail, 'SurfShop 40/64', "Message depuis mon propre site SurfShop 40/64" , $content);


            $message = (new \Swift_Message('SurfShop 40/64 => Vous avez reçu un message depuis votre site SurfShop 40/64 '))
            ->setFrom('surfshop64@gmail.com')
            ->setTo('aurelien.landouer@hotmail.fr')
            ->setBody(
                $this->renderView(
                    'emails/contact.html.twig',
                    [
                        'message' => $form->get('description')->getData(),
                        'prenom' => $form->get('prenom')->getData(),
                        'nom' => $form->get('nom')->getData(),
                        'mail' => $form->get('email')->getData()
                    ]
                    ),'text/html'
            );
        $this->mailer->send($message);

        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
