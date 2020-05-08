<?php

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\AdminExtensionControllerTrait as AdminExtensionControllerTrait;;
use AlterPHP\EasyAdminExtensionBundle\Security\AdminAuthorizationChecker;

use AlterPHP\EasyAdminExtensionBundle\EasyAdminExtensionBundle;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseEasyAdminControler;

class AdminController extends BaseEasyAdminControler
{

    use AdminExtensionControllerTrait;


    private $encoder;
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserPasswordEncoderInterface $encoder, PostRepository $postRepository, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedServices(): array
    {
        return \array_merge(parent::getSubscribedServices(), [AdminAuthorizationChecker::class]);
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



    /**
     * @Route("/", name="easyadmin")
     */
    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }



    public function voirAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->request->query->get('entity');
        switch ($entity) {
            case $entity === "Post" || $entity === "PostToValidate":
                $post = $this->postRepository->find($id);
                return $this->redirectToRoute('post.show', ['id' => $id]);

                break;

            case $entity === "Comment" || $entity === "CommentToValidate":
                $comment = $this->commentRepository->find($id);
                return $this->redirectToRoute('post.show', ['id' => $comment->getPost()->getId(), '_fragment' => "comment-" . $comment->getId() . ""]);


                break;

            case $entity === "User" :
                $user = $this->userRepository->find($id);

                return $this->redirectToRoute('account.show', ['id' => $id]);

            default:

                break;
        }
    }

}
