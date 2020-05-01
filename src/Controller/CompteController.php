<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/account", name="account.")
 */
class CompteController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/settings", name="settings");
     * Method({"GET","POST"})
     */
    public function settings(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Recupere L'email et l'avatar mais pas le nouveau password
            $user = $form->getData();

            $actualPassword = $form->get("actualPassword")->getData();
            $newPassword = $form->get("newPassword")->getData();
            if ($actualPassword !== "**********") {

                if ($this->passwordEncoder->isPasswordValid($user, $actualPassword)) {
                    //C'est le bon mot de passe on modifie le mot de passe de l'user
                    $encoded = $encoder->encodePassword($user, $newPassword);
                    $user->setPassword($encoded);
                }

            }


            $this->em->persist($user);
            $this->em->flush();
            $user->setImageFile(null);
            $this->addFlash('success','Modification enregistrer avec succés');

            return $this->redirectToRoute('account.settings');
        }

        return $this->render('compte/settings.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}", name="show");
     */
    public function show($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);


        return $this->render('compte/show.html.twig', [
            'user' => $user,

        ]);

    }


    /**
     * @Route("/delete", name="delete");
     */
    public function delete()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        return $this->render('compte/settings.html.twig', [
            'user' => $user,

        ]);

    }


}
