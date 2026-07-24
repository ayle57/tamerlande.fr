<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $username = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 5000)]
    private ?string $bio = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(choices: ["active", "banned"])]
    private ?string $status = null;

    /**
     * Catégories créées par l'utilisateur
     *
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'creator')]
    private Collection $categories;


    /**
     * Discussions créées par l'utilisateur
     *
     * @var Collection<int, Thread>
     */
    #[ORM\OneToMany(targetEntity: Thread::class, mappedBy: 'author')]
    private Collection $threads;


    /**
     * Messages écrits par l'utilisateur
     *
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;


    /**
     * Ressources appartenant à l'utilisateur
     *
     * @var Collection<int, Resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'owner')]
    private Collection $resources;


    /**
     * Notifications reçues
     *
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;


    /**
     * Logs utilisateur
     *
     * @var Collection<int, Log>
     */
    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: 'user')]
    private Collection $logs;


    /**
     * Historique des connexions
     *
     * @var Collection<int, LoginLog>
     */
    #[ORM\OneToMany(targetEntity: LoginLog::class, mappedBy: 'user')]
    private Collection $loginLogs;


    /**
     * Tokens de connexion
     *
     * @var Collection<int, RememberToken>
     */
    #[ORM\OneToMany(targetEntity: RememberToken::class, mappedBy: 'user')]
    private Collection $rememberTokens;


    /**
     * Catégories dont l'utilisateur est membre
     *
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'members')]
    private Collection $memberCategories;


    /**
     * Posts likés par l'utilisateur
     *
     * @var Collection<int, Post>
     */
    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'likedBy')]
    private Collection $likedPosts;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->threads = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->loginLogs = new ArrayCollection();
        $this->rememberTokens = new ArrayCollection();
        $this->memberCategories = new ArrayCollection();
        $this->likedPosts = new ArrayCollection();

        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUsername(): ?string
    {
        return $this->username;
    }


    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }


    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function eraseCredentials(): void
    {
    }


    public function getBio(): ?string
    {
        return $this->bio;
    }


    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }


    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }


    public function getCategories(): Collection
    {
        return $this->categories;
    }


    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setCreator($this);
        }

        return $this;
    }


    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }


    public function getThreads(): Collection
    {
        return $this->threads;
    }


    public function addThread(Thread $thread): static
    {
        if (!$this->threads->contains($thread)) {
            $this->threads->add($thread);
            $thread->setAuthor($this);
        }

        return $this;
    }


    public function removeThread(Thread $thread): static
    {
        $this->threads->removeElement($thread);

        return $this;
    }


    public function getPosts(): Collection
    {
        return $this->posts;
    }


    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }


    public function removePost(Post $post): static
    {
        $this->posts->removeElement($post);

        return $this;
    }


    public function getResources(): Collection
    {
        return $this->resources;
    }


    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setOwner($this);
        }

        return $this;
    }


    public function removeResource(Resource $resource): static
    {
        $this->resources->removeElement($resource);

        return $this;
    }


    public function getNotifications(): Collection
    {
        return $this->notifications;
    }


    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }


    public function removeNotification(Notification $notification): static
    {
        $this->notifications->removeElement($notification);

        return $this;
    }


    public function getLogs(): Collection
    {
        return $this->logs;
    }


    public function addLog(Log $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setUser($this);
        }

        return $this;
    }


    public function getLoginLogs(): Collection
    {
        return $this->loginLogs;
    }


    public function addLoginLog(LoginLog $loginLog): static
    {
        if (!$this->loginLogs->contains($loginLog)) {
            $this->loginLogs->add($loginLog);
            $loginLog->setUser($this);
        }

        return $this;
    }


    public function getRememberTokens(): Collection
    {
        return $this->rememberTokens;
    }


    public function addRememberToken(RememberToken $rememberToken): static
    {
        if (!$this->rememberTokens->contains($rememberToken)) {
            $this->rememberTokens->add($rememberToken);
            $rememberToken->setUser($this);
        }

        return $this;
    }


    public function getMemberCategories(): Collection
    {
        return $this->memberCategories;
    }


    public function addMemberCategory(Category $category): static
    {
        if (!$this->memberCategories->contains($category)) {
            $this->memberCategories->add($category);
        }

        return $this;
    }


    public function removeMemberCategory(Category $category): static
    {
        $this->memberCategories->removeElement($category);

        return $this;
    }


    public function getLikedPosts(): Collection
    {
        return $this->likedPosts;
    }


    public function addLikedPost(Post $post): static
    {
        if (!$this->likedPosts->contains($post)) {
            $this->likedPosts->add($post);
        }

        return $this;
    }


    public function removeLikedPost(Post $post): static
    {
        $this->likedPosts->removeElement($post);

        return $this;
    }
}
