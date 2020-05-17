<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $textComment;





    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $approved;




    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="comment", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="comment", orphanRemoval=true)
     */
    private $reports;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Comment", inversedBy="commentsReply")
     */
    private $replyComments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Comment", mappedBy="replyComments")
     */
    private $commentsReply;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReply;




    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->replyComments = new ArrayCollection();
        $this->commentsReply = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextComment(): ?string
    {
        return $this->textComment;
    }

    public function setTextComment(string $textComment): self
    {
        $this->textComment = $textComment;

        return $this;
    }




    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }





    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        }

        return $this;
    }
    function __toString()
    {
     return $this->textComment;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setComment($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getComment() === $this) {
                $report->setComment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getReplyComments(): Collection
    {
        return $this->replyComments;
    }

    public function addReplyComment(self $replyComment): self
    {
        if (!$this->replyComments->contains($replyComment)) {
            $this->replyComments[] = $replyComment;
        }

        return $this;
    }

    public function removeReplyComment(self $replyComment): self
    {
        if ($this->replyComments->contains($replyComment)) {
            $this->replyComments->removeElement($replyComment);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCommentsReply(): Collection
    {
        return $this->commentsReply;
    }

    public function addCommentsReply(self $commentsReply): self
    {
        if (!$this->commentsReply->contains($commentsReply)) {
            $this->commentsReply[] = $commentsReply;
            $commentsReply->addReplyComment($this);
        }

        return $this;
    }

    public function removeCommentsReply(self $commentsReply): self
    {
        if ($this->commentsReply->contains($commentsReply)) {
            $this->commentsReply->removeElement($commentsReply);
            $commentsReply->removeReplyComment($this);
        }

        return $this;
    }

    public function getIsReply(): ?bool
    {
        return $this->isReply;
    }

    public function setIsReply(bool $isReply): self
    {
        $this->isReply = $isReply;

        return $this;
    }


}
