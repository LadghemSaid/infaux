<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 * @UniqueEntity("email",message="Cet email est déjà utilisé")
 *
 */
class User implements UserInterface,\Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180,)
     */
    private $username;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    private $salt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true )
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true )
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="user" )
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="friends")
     */
    private $friendList;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="friendList" )
     */
    private $friends;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="user" )
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user", orphanRemoval=true )
     */
    private $notifications;
    private $notificationNotSeen;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NotificationMessagerie", mappedBy="user", orphanRemoval=true )
     */
    private $notificationsMessagerie;
    private $notificationsMessagerieNotSeen;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="followedBy" )
     */
    private $postFollowed;







    /**
     * @Vich\UploadableField(mapping="users_images", fileNameProperty="image")
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"})
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage = "Please upload a valid valid IMAGE"
     * )
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string")
     * @var string|null
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTimeInterface|null
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $displaySetting;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $visibility;


    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="user")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user")
     */
    private $messages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountConfirmed;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->friendList = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->notificationsMessagerie = new ArrayCollection();
        $this->postFollowed = new ArrayCollection();
        $this->updatedAt= new \DateTimeImmutable();
        $this->createdAt= new \DateTimeImmutable();
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();

    }


    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->image,

        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->image,

            ) = unserialize($serialized);
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setUser($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getUser() === $this) {
                $participant->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }


    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }



    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * * @param File|UploadedFile|null $imageFile
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile =null): void
    {
        $this->imageFile = $imageFile;
        if(null !== $imageFile){
            $this->updatedAt= new \DateTimeImmutable();
        }
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriendList(): Collection
    {
        return $this->friendList;
    }

    public function addFriendList(self $friendList): self
    {
        if (!$this->friendList->contains($friendList)) {
            $this->friendList[] = $friendList;
        }

        return $this;
    }

    public function removeFriendList(self $friendList): self
    {
        if ($this->friendList->contains($friendList)) {
            $this->friendList->removeElement($friendList);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
            $friend->addFriendList($this);
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
            $friend->removeFriendList($this);
        }

        return $this;
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
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getUser() === $this) {
                $report->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|NotificationMessagerie[]
     */
    public function getNotificationsMessagerie(): Collection
    {
        return $this->notificationsMessagerie;
    }

    public function addNotificationMessagerie(NotificationMessagerie $notificationsMessagerie): self
    {
        if (!$this->notificationsMessagerie->contains($notificationsMessagerie)) {
            $this->notificationsMessagerie[] = $notificationsMessagerie;
            $notificationsMessagerie->setUser($this);
        }

        return $this;
    }

    public function removeNotificationsMessagerie(NotificationMessagerie $notificationsMessagerie): self
    {
        if ($this->notificationsMessagerie->contains($notificationsMessagerie)) {
            $this->notificationsMessagerie->removeElement($notificationsMessagerie);
            // set the owning side to null (unless already changed)
            if ($notificationsMessagerie->getUser() === $this) {
                $notificationsMessagerie->setUser(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Post[]
     */
    public function getPostFollowed(): Collection
    {
        return $this->postFollowed;
    }

    public function addPostFollowed(Post $postFollowed): self
    {
        if (!$this->postFollowed->contains($postFollowed)) {
            $this->postFollowed[] = $postFollowed;
            $postFollowed->addFollowedBy($this);
        }

        return $this;
    }

    public function removePostFollowed(Post $postFollowed): self
    {
        if ($this->postFollowed->contains($postFollowed)) {
            $this->postFollowed->removeElement($postFollowed);
            $postFollowed->removeFollowedBy($this);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotificationNotSeen()
    {
        $notificationNotSeen =[];
        foreach ($this->notifications as $notif){
            if(!$notif->getSeen()){
                array_push($notificationNotSeen,$notif);
            }
        }
        return $notificationNotSeen;
    }

    /**
     * @param mixed $notificationNotSeen
     */
    public function setNotificationNotSeen($notificationNotSeen): void
    {
        $this->notificationNotSeen = $notificationNotSeen;
    }

    /**
     * @return mixed
     */
    public function getNotificationsMessagerieNotSeen()
    {
        $notificationsMessagerieNotSeen =[];
        foreach ($this->notificationsMessagerie as $notif){
            if(!$notif->getSeen()){
                array_push($notificationsMessagerieNotSeen,$notif);
            }
        }
        return $notificationsMessagerieNotSeen;
    }

    /**
     * @param mixed $notificationNotSeen
     */
    public function setNotificationsMessagerieNotSeen($notificationsMessagerieNotSeen): void
    {
        $this->notificationsMessagerieNotSeen = $notificationsMessagerieNotSeen;
    }



    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDisplaySetting(): ?string
    {
        return $this->displaySetting;
    }

    public function setDisplaySetting(string $displaySetting): self
    {
        $this->displaySetting = $displaySetting;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getAccountConfirmed(): ?bool
    {
        return $this->accountConfirmed;
    }

    public function setAccountConfirmed(bool $accountConfirmed): self
    {
        $this->accountConfirmed = $accountConfirmed;

        return $this;
    }


}
