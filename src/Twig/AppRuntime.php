<?php

// src/Twig/AppRuntime.php
namespace App\Twig;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(CommentRepository $commentRepository, PostRepository $postRepository)
    {
        // this simple example doesn't define any dependency, but in your own
        // extensions, you'll need to inject services using this constructor
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    public function getCommentFunction($postId, $order)
    {
        $arrayComment= $this->commentRepository->findBy([
            'post'=>$postId,
            'approved'=>true,

        ],"createdAt");


        return $arrayComment;

    }


    public function formatCommentFunction($arrayComment, $order)
    {
        if ($order == 'DESC') {
            usort($arrayComment, function ($item1, $item2) {
                if ($item1->getCreatedAt() == $item2->getCreatedAt()) return 0;
                return $item1->getCreatedAt() < $item2->getCreatedAt() ? -1 : 1;
            });
        } else {
            usort($arrayComment, function ($item1, $item2) {
                if ($item1->getCreatedAt() == $item2->getCreatedAt()) return 0;
                return $item1->getCreatedAt() > $item2->getCreatedAt() ? -1 : 1;
            });
        }

        foreach ($arrayComment as $comment) {
            if ($comment->getApproved() != true) {
                array_unshift($arrayComment, $comment);
            }
        }


        return $arrayComment;

    }

    public function castToArrayFunction($stdClassObject)
    {
        $response = array();
        foreach ($stdClassObject as $key => $value) {
            $response[] = array($key, $value);
        }
        return $response;

    }

    public function commentMostLikeFunction($comments)
    {
        $cmt = 0;
        $last = null;

        foreach ($comments as $comment) {
            if (count($comment->getLikes()) >= $cmt) {
                $cmt = count($comment->getLikes());
                $last = $comment;
            } else {

            }
        }

        return $last;

    }

}
