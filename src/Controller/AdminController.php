<?php
namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\EasyAdminExtensionBundle;
use App\Entity\Posts;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class AdminController extends EasyAdminExtensionBundle
{
    private $encoder;
    public function __construct( UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    protected function prePersistUserEntity(User $user)
    {
        $encodedPassword = $this->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
    }

    protected function preUpdateUserEntity(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }
        $encodedPassword = $this->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);
    }

    private function encodePassword(User $user, $password)
    {
        //$passwordEncoderFactory = $this->get('security.encoder_factory');
        //$encoder = $passwordEncoderFactory->getEncoder($user);
        return $this->encoder->encodePassword($password, $user->getSalt());
    }



}
