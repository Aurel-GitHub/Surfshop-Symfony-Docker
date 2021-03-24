<?php

namespace App\Controller;

use App\Entity\Order; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Classes\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $em,Cart $cart, $reference)
    {
        $products_for_stripe = [];
        // env prod  $YOUR_DOMAIN= monsiteheroku.com
        $YOUR_DOMAIN = 'http://localhost:8741';

        $order = $em->getRepository(Order::class)->findOneByReference($reference);

        if(!$order){
            return $response = new  JsonResponse(['error' => 'order']);
        }

        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object = $em->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/img/".$product_object->getIllustration()],
                        ],
                    ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                    ],
                ],
            'quantity' => 1,
        ];

        /**
         * dev
         */
         Stripe::setApiKey('KEY!!!');
        /**
         * prod
         */
        // Stripe::setApiKey('KEY PROD !!!');




        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$products_for_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $em->flush();

        return $response = new  JsonResponse(['id' => $checkout_session->id]);
    }
}
