<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CompteController extends AbstractController
{

    /**
     * @Route("/compte/{id}/edit", name="edit_compte")->where('id','[0-9]+');
     * Method({"GET","POST"})
     */
    public function edit(Request $request, $id){
        $user= $this->getDoctrine()->getRepository(User::class)->find($id);

        $form= $this->createFormBuilder($user)
        ->add('imageFile',VichImageType::class)
        ->add('save', SubmitType::class, array(
            'label'=>'Editer',
            'attr'=>array('class'=>'btn btn-primary mt-3')
        ))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $image=$form->getData();
            $file=$image->getImage();




            move_uploaded_file($file,"public/uploads/images/users");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirect('/');
        }

        return $this->render('compte/edit.html.twig',array('form'=>$form->createView()));
    }
    /**
     * @Route("/compte/{id}", name="compte")->where('id','[0-9]+');
     */
    public function index()
    {

        $user = $this->getUser();

        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
            'user'=> $user,

        ]);

    }

  //  /**
  //   * @Route("/compte/{id}/save", name="save_compte")->where('id','[0-9]+');
  //   */
  //  public function save(){
  //      $entityManger = $this->getDoctrine()->getManager();
  //     $user = new User();
  //      $user->setImageFile($user->getImageFile());
  //      $user->setUsername('Test change UserName');


//        $entityManger->persist($user);

  //      $entityManger->flush();

    //    return new Response("Sauvegarde de l'utilisateur ".$user->getId()." faite");
    //}
}
