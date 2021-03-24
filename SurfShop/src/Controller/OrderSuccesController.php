<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Classes\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart,$stripeSessionId, \Swift_Mailer $mailer): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0){

            $cart->remove();

            $order->setState(1);
            
            /**
             * Commenté le temps du test
             */
             $this->em->flush();



            $message = (new \Swift_Message('Votre commande sur Surfshop 40/64 est bien validée'))
                ->setFrom('surfshop64@gmail.com')
                ->setTo($order->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/order_confirm.html.twig',
                        [
                            'nom' => $order->getUser()->getFirstname()
                        ]
                        ),'text/html'
                );

            $mailer->send($message);
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
