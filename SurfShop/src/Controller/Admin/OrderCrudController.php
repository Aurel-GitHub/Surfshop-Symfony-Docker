<?php

namespace App\Controller\Admin;

use App\Classes\Mail;
use App\Entity\Order;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class OrderCrudController extends AbstractCrudController
{

    private $em;
    private $crudUrlGenerator;
    private $mailer;

    public function __construct(EntityManagerInterface $em, CrudUrlGenerator $crudUrlGenerator, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->mailer = $mailer;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    
    public function configureActions(Actions $actions): Actions
    {

        $updatePreparation = Action::new('updatePreparation', 'Preparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');


        return $actions
            ->add('index', 'detail')
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)        
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
        
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);

        $this->em->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande ".$order->getReference()."<u> est en cours de préparation<u/>.</strong></span>");
        
        $url = $this->crudUrlGenerator->build()
                ->setController(OrderCrudController::class)
                ->setAction('index')
                ->generateUrl();    

        $message = (new \Swift_Message('Votre commande n°'.$order->getReference().' est en cours de préparation.'))
        ->setFrom('surfshop64@gmail.com')
        ->setTo($order->getUser()->getEmail())
        ->setBody(
            $this->renderView(
                'emails/order_preparation.html.twig',
                [
                    'nom' => $order->getUser()->getFirstname(),
                    'commande' => $order->getReference()
                ]
                ),'text/html'
        );
    $this->mailer->send($message);

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);

        $this->em->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande ".$order->getReference()."<u> est en cours de livraison<u/>.</strong></span>");
        
        $url = $this->crudUrlGenerator->build()
                ->setController(OrderCrudController::class)
                ->setAction('index')
                ->generateUrl();

                $message = (new \Swift_Message('Votre commande n°'.$order->getReference().' est en cours de livraison.'))
                ->setFrom('surfshop64@gmail.com')
                ->setTo($order->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/order_delivery.html.twig',
                        [
                            'nom' => $order->getUser()->getFirstname(),
                            'commande' => $order->getReference()
                        ]
                        ),'text/html'
                );
            $this->mailer->send($message);
                
        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passé le',), 
            TextField::new('user.fullName', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            TextField::new('carrierName', 'Livreur'),
            ChoiceField::new('state', 'Statut')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
    
}
