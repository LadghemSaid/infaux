<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => false,
                //    'data_class'=>null
            ])
            ->add('description', TextType::class, [
                'constraints' => array(
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas etre vide'
                    ])
                )])
            ->add('email', EmailType::class, [
                'constraints' => array(
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas etre vide'
                    ])
                )])
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
            ->add('displaySetting', TextType::class, [
            ])
            ->add('visibility', TextType::class, [
                'mapped' => false,
                'attr'=>[
                    'value'=>'all'
                ]
            ])
            ->add('submit', SubmitType::class, [

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
