<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TutoController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/inscription/avatar", name="tuto.avatar")
     */
    public function avatar(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();



        $formAvatar = $this->get('form.factory')->createNamedBuilder( 'formAvatar')
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


        $formAvatar->handleRequest($request);
        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {


            //Recupere L'email et l'avatar mais pas le nouveau password

            $user->setImageFile($formAvatar->getData('imageFile')['imageFile']);
            $this->em->persist($user);
            $this->em->flush();
            $user->setImageFile(null);
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('tuto.description');
        }



        //Cree un formulaire
        return $this->render('tuto/avatar.html.twig', [
            'formAvatar' => $formAvatar->createView(),

        ]);
    }

    /**
     * @Route("/inscription/description", name="tuto.description")
     */
    public function description(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $formDescription =  $this->get('form.factory')->createNamedBuilder( 'formDescription')
            ->add('description', TextareaType::class, [
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


        $formDescription->handleRequest($request);
        if ($formDescription->isSubmitted() && $formDescription->isValid()) {
            $user->setDescription($formDescription->get('description')->getData());
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Modification enregistrer avec succés');

            return $this->redirectToRoute('index');
        }

        return $this->render('tuto/description.html.twig', [
            'formDescription' => $formDescription->createView(),
        ]);
    }

    /**
     * @Route("/inscription/skip", name="tuto.skip")
     */
    public function skip()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        return $this->render('tuto/skip.html.twig', [
            'controller_name' => 'TutoController',
        ]);
    }
}
