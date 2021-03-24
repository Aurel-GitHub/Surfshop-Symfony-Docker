<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegisterController extends AbstractController
{
    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer){
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder ,GuardAuthenticatorHandler $guardHandler,LoginFormAuthenticator $authenticator): Response
    {

        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();

            $search_email = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail());

            $password  = $encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                $this->em->persist($user);
                $this->em->flush();

                $message = (new \Swift_Message('Bienvenue sur SurfShop 40/64'))
                ->setFrom('surfshop64@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/new_account.html.twig',
                        [
                            'nom' => $user->getFullName()
                        ]
                        ),'text/html'
                );
            $this->mailer->send($message);
    
                $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main');

                $this->addFlash('succes' ,"Votre inscription a bien été enregistrée.");

                return $this->redirectToRoute('home');

        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
