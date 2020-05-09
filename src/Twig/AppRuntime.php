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
        ], array('created_at' => $order));


        return $arrayComment;

    }






    public function commentMostLikeFunction($postId,$limit)
    {
        $comment = $this->commentRepository->findBycommentMostLike($postId,$limit);

        dd($comment);
        if(count($comment) > 0){
            return $comment[0];
        }else{
            return $comment;

        }
    }

}
