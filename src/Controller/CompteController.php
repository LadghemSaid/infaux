<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
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


        $formAvatar = $this->get('form.factory')->createNamedBuilder('formAvatar')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => false,
                //    'data_class'=>null
            ])
            ->add('submit', SubmitType::class, [
            ])
            ->getForm();

        $formDescription = $this->get('form.factory')->createNamedBuilder('formDescription')
            ->add('description', TextType::class, [
                'attr' => [
                    'value' => $user->getDescription()
                ],
                'constraints' => array(
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas etre vide'
                    ])
                )])
            ->add('submit', SubmitType::class, [

            ])
            ->getForm();

        $formEmail = $this->get('form.factory')->createNamedBuilder('formEmail')
            ->add('email', EmailType::class, [
                'attr' => [
                    'value' => $user->getEmail()
                ],
                'required' => false,
                'constraints' => array(
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas etre vide'
                    ])
                )])
            ->add('submit', SubmitType::class, [

            ])
            ->getForm();


        $formMdp = $this->get('form.factory')->createNamedBuilder('formMdp')
            ->add('actualPassword', PasswordType::class, [
                "mapped" => false,
                'constraints' => array(

                    new Length([
                        'min' => 2,
                        'minMessage' => 'Longueur minimal du mot de passe est de 5 charactere',
                        'max' => 20,
                        'maxMessage' => 'Longueur maximal du mot de passe est de 20 charactere',
                    ]),
                ),


            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent etre identique !',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],

                "mapped" => false,
                'constraints' => array(

                    new Length([
                        'min' => 5,
                        'minMessage' => 'Longueur minimal du mot de passe est de 5 charactere',
                        'max' => 20,
                        'maxMessage' => 'Longueur maximal du mot de passe est de 20 charactere',
                    ]),

                ),

            ])
            ->add('submit', SubmitType::class, [

            ])
            ->getForm();

        $formDisplaySetting = $this->get('form.factory')->createNamedBuilder('formDisplaySetting')
            ->add('displaySetting', ChoiceType::class, [
                'choices' => [
                    'Les plus populaires' => 'popular',
                    'Les plus recents' => 'recent',
                    'De mes abonnements' => 'friends',
                ],
            ])
            ->add('submit', SubmitType::class, [

            ])
            ->getForm();

        $formVisibility = $this->get('form.factory')->createNamedBuilder('formVisibility')
            ->add('visibility', ChoiceType::class, [
                'mapped' => false,
                'attr' => [
                    'value' => 'all'
                ],
                'choices' => [
                    'Tout le monde' => 'public',
                    'Seulement mes abonnés' => 'friends'
                ],
            ])
            ->add('submit', SubmitType::class, [

            ])
            ->getForm();


        $formMdp->handleRequest($request);
        if ($formMdp->isSubmitted() && $formMdp->isValid()) {
            $actualPassword = $formMdp->get("actualPassword")->getData();
            $newPassword = $formMdp->get("newPassword")->getData();
            if ($actualPassword !== "**********") {

                if ($this->passwordEncoder->isPasswordValid($user, $actualPassword)) {
                    //C'est le bon mot de passe on modifie le mot de passe de l'user
                    $encoded = $encoder->encodePassword($user, $newPassword);
                    $user->setPassword($encoded);
                }

            }
        }
        $formEmail->handleRequest($request);
        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $user->setEmail($formEmail->get('email')->getData());
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('account.settings');
        }

        $formDescription->handleRequest($request);
        if ($formDescription->isSubmitted() && $formDescription->isValid()) {
            $user->setDescription($formDescription->get('description')->getData());
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('account.settings');
        }


        $formDisplaySetting->handleRequest($request);
        if ($formDisplaySetting->isSubmitted() && $formDisplaySetting->isValid()) {
            $user->setDisplaySetting($formDisplaySetting->get('displaySetting')->getData());
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('account.settings');
        }

        $formVisibility->handleRequest($request);
        if ($formVisibility->isSubmitted() && $formVisibility->isValid()) {
            dd('ok5');
        }

        $formAvatar->handleRequest($request);
        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {


            //Recupere L'email et l'avatar mais pas le nouveau password
            //dd($this->getParameter('app.path.user_images').$formAvatar->getData('imageFile')['imageFile']->getClientOriginalName());
            $user->setImageFile($formAvatar->getData('imageFile')['imageFile']);
            $this->em->persist($user);
            $this->em->flush();
            $user->setImageFile(null);
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('account.settings');
        }

        return $this->render('compte/settings.html.twig', [
            'user' => $this->getUser(),
            'formAvatar' => $formAvatar->createView(),
            'formDescription' => $formDescription->createView(),
            'formEmail' => $formEmail->createView(),
            'formMdp' => $formMdp->createView(),
            'formDisplaySetting' => $formDisplaySetting->createView(),
            'formVisibility' => $formVisibility->createView(),
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
