<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerService $mailerService, \Swift_Mailer $mailer): Response
    {

       $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ( $form->isSubmitted() &&  $form->isValid() ) {
            // encode the plain password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles(array('ROLE_USER'));
            $user->setImage('false');
            $user->setConfirmationToken($this->generateToken()); //Ajouter le champ a l'entité user
            //$user->setImage('https://api.adorable.io/avatars/60/'.$user->getUsername().'.png' );
            $user->setDescription('. . .');
            $user->setDisplaySetting('recent');
            $user->setVisibility('public');
            $user->setAccountConfirmed(false);

            //Envoie d'email de confirmation
            $token = $user->getConfirmationToken();
            $mailerService->sendToken( $token,$user, 'registration.html.twig');
            $this->addFlash('success', 'Votre inscription a été validée, vous aller recevoir un email de confirmation pour activer votre compte et pouvoir vous connecté');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email


            return $this->redirectToRoute('app_login');

            //return $this->redirectToRoute('tuto.avatar');
        }


        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/account/confirm/{token}/{email}", name="confirm_account")
     * @param $token
     * @param $username
     * @return Response
     */
    public function confirmAccount($token,GuardAuthenticatorHandler $security, $email,AppCustomAuthenticator $login,Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        $tokenExist = $user->getConfirmationToken();
        if($token === $tokenExist) {
            $user->setConfirmationToken(null);
            $user->setAccountConfirmed(true);
            $em->persist($user);
            $em->flush();
            $security->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $login,
                'main'
            );
            return $this->redirectToRoute('tuto.avatar');
        } else {
            return $this->render('security/token-expire.html.twig');
        }
    }
    /**
     * @Route("/send-token-confirmation", name="send_confirmation_token")
     * @param Request $request
     * @param MailerService $mailerService
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function sendConfirmationToken(Request $request, MailerService $mailerService, \Swift_Mailer $mailer): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->request->get('email');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        if($user === null) {
            $this->addFlash('not-user-exist', 'utilisateur non trouvé');
            return $this->redirectToRoute('app_register');
        }
        $user->setConfirmationToken($this->generateToken());
        $em->persist($user);
        $em->flush();
        $token = $user->getConfirmationToken();
        $email = $user->getEmail();
        $username = $user->getUsername();
        $mailerService->sendToken( $token, $user, 'registration.html.twig');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/mot-de-passe-oublier", name="forgotten_password")
     * @param Request $request
     * @param MailerService $mailerService
     * @param \Swift_Mailer $mailer
     * @return Response
     * @throws \Exception
     */
    public function forgottenPassword(Request $request, MailerService $mailerService, \Swift_Mailer $mailer): Response
    {
        if($request->isMethod('POST')) {
            $email = $request->get('email');
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
            if($user === null) {
                $this->addFlash('user-error', 'utilisateur non trouvé');
                return $this->redirectToRoute('app_register');
            }
            $user->setTokenPassword($this->generateToken());
            $user->setCreatedTokenPasswordAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $token = $user->getTokenPassword();
            $email = $user->getEmail();
            $username = $user->getUsername();
            $mailerService->sendToken( $token,$user, 'forgotten_password.html.twig');
            return $this->redirectToRoute('home');
        }
        return $this->render('security/forgotten_password.html.twig');
    }


    /**
     * @Route("/reset-password/{token}", name="reset_password")
     * @param Request $request
     * @param $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function resetPassword(Request $request, $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $em->getRepository(User::class)->findOneBy(['tokenPassword' => $token]);
            if($user === null) {
                $this->addFlash('not-user-exist', 'utilisateur non trouvé');
                return $this->redirectToRoute('app_register');
            }
            $user->setTokenPassword(null);
            $user->setCreatedTokenPasswordAt(null);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $em->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset-password.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

}
