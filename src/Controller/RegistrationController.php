<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,GuardAuthenticatorHandler $security,AppCustomAuthenticator $login): Response
    {

       $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ( $form->isSubmitted() &&  $form->isValid() ) {
            // encode the plain password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles(array('ROLE_USER') );
            $user->setImage('false' );
            //$user->setImage('https://api.adorable.io/avatars/60/'.$user->getUsername().'.png' );
            $user->setDescription('. . .' );
            $user->setDisplaySetting('recent' );
            $user->setVisibility('public' );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
           $security->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $login,
                    'main'
                );

            return $this->redirectToRoute('tuto.avatar');
        }


        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

}
