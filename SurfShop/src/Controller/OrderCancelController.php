<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{    
    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        $message = (new \Swift_Message('Paiement échoué'))
                ->setFrom('surfshop64@gmail.com')
                ->setTo($order->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/order_cancel.html.twig',
                        [
                            'nom' => $order->getUser()->getFirstname()
                        ]
                        ),'text/html'
                );
            $this->mailer->send($message);

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
