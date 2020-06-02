<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\LiipImagineBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

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
    /**
     * @var LiipImagineBundle
     */
    private $liipImagineBundle;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, FilterManager $liipImagineBundle)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->liipImagineBundle = $liipImagineBundle;
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
                'required' => true,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => false,
                'constraints' => array(
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas etre vide'
                    ]),
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Merci de fournir une image au format valide",
                        'maxSizeMessage' => "Ton image est trop grande !",
                        'uploadIniSizeErrorMessage' => "Ton image est trop grande !",
                        'disallowEmptyMessage' => "Merci de fournir une image valide",
                        'notFoundMessage' => "Merci de fournir une image valide",
                        'notReadableMessage' => "Merci de fournir une image valide",
                        'uploadErrorMessage' => "Merci de fournir une image valide",
                    ])
                )


                //    'data_class'=>null
            ])
            ->add('submit', SubmitType::class, [
            ])
            ->getForm();

        $formDescription = $this->get('form.factory')->createNamedBuilder('formDescription')
            ->add('description', TextType::class, [
                'attr' => [
                    'value' => $user->getDescription(),
                    'maxlength' => "500",
                    'minlength' => "0",
                    'required' => "true",
                ],
                'constraints' => array(
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas etre vide'
                    ]),
                    new Length([
                            'max' => 500,
                            'min' => 0,
                            'maxMessage' => "Ta description est trop longue elle doit faire au maximum {{ limit }} caractères",
                            'minMessage' => "Ta description est trop courte elle doit faire au minimum {{ limit }} caractères",
                            'allowEmptyString' => true

                        ]
                    )

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
                'attr' => [
                    'maxlength' => "20",
                    'minlength' => "5",
                    'required' => "true",
                ],
                "mapped" => false,
                'constraints' => array(

                    new Length([
                        'min' => 5,
                        'minMessage' => 'Longueur minimal du mot de passe est de {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Longueur maximal du mot de passe est de {{ limit }} caractères',
                        'allowEmptyString' => false
                    ]),
                ),


            ])
            ->add('newPassword', RepeatedType::class, [
                'attr' => [
                    'maxlength' => "20",
                    'minlength' => "5",
                    'required' => "true",
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent etre identique !',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options' => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmez votre mot de passe'],

                "mapped" => false,
                'constraints' => array(

                    new Length([
                        'min' => 5,
                        'minMessage' => 'Longueur minimal du mot de passe est de {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Longueur maximal du mot de passe est de {{ limit }} caractères',
                        'allowEmptyString' => false

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
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->addFlash('success', 'Modification enregistrer avec succés');

                } else {
                    $this->addFlash('error', 'Mot de passe invalide');
                }


            } else {
                $this->addFlash('error', 'Mot de passe invalide');

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
            $user->setVisibility($formVisibility->get('visibility')->getData());
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('account.settings');
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
