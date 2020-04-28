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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/settings", name="settings");
     * Method({"GET","POST"})
     */
    public function settings(Request $request,UserFormType $userFormType)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $this->em->persist($user);
            $this->em->flush();
            $user->setImageFile(null);

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
        $user = $this->getUser();

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
