<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {

        if($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($request->get('email')){
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user){
                // 1 : Enrengistrement en base de la demande d'un nouveau mot de passe
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTime());
                $this->em->persist($reset_password);
                $this->em->flush();

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                //////////////////////////////////////////////////////////////////////
                ///
                /// TODO !!!!!
                /// EN PROD MODIFIER LE <a href="..."></a> SINON PAS D'URL VALIDE 
                /// POUR LA CRÉATION D'UN NOUVEAU PASSWORD
                ///
                //////////////////////////////////////////////////////////////////////

                $message = (new \Swift_Message('Réinitialiser votre mot de passe sur SurfShop 40/64'))
                ->setFrom('surfshop64@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/reset_password.html.twig',
                        [
                            'nom' => $user->getFullName(),
                            'url' =>  $url
                        ]
                        ),'text/html'
                );
            $this->mailer->send($message);

                $this->addFlash('notice', 'Vous allez recevoir dans quelques secondes un mail avec la procédure à suivre pour réinitialiser votre mot de passe.');

            }else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

       /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordEncoderInterface $encoder): Response
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        $now = new DateTime();

        // Vérification des 3 heures de validités du lien
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveller');
            
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()  ){
            $new_pwd = $form->get('new_password')->getData();
            
            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);

            $reset_password->getUser()->setPassword($password);
            $this->em->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
