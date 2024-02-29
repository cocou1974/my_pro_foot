<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
// Validation de formulaire
use Symfony\Component\Validator\Constraints as Assert;
//Doctrine. Timestampable.

use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email cet email existe déjà')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]  //Validation de formulaire
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le prénom ne doit pas dépasser{{ limit }} caractère.',
    )]
     #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_'áàâäãåçéèêëíìîïñóòôöõúùûüýÿœæÁÀÂÄÃÅCÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: "Le prénom doit contenir uniquement des lettres, des chiffres le tiret du milieu de l\'undesscore"
    )]
     #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]  //Validation de formulaire
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom ne doit pas dépasser{{ limit }} caractères.",
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_'áàâäãåçéèêëíìîïñóòôöõúùûüýÿœæÁÀÂÄÃÅCÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: "Le nom doit contenir uniquementdes lettres, des chiffres le tiret du milieu de l\'undesscore"
    )]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;


    #[Assert\NotBlank(message: "L'email est obligatoire.")]  
    #[Assert\Length(
        max: 180,
        maxMessage: "L'email ne doit pas dépasser{{ limit }} caractère.",
    )]
    #[Assert\Email(
        message: "L' email {{ value }} est invalide.",
    )]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;


    #[ORM\Column]
    private array $roles = [];



    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(message: "Le mot de passe  obligatoire.")]  
    #[Assert\Length(
        //Convention CNIL
        min: 12,
        max: 255,
        minMessage: 'Le mot de passe ne doit contenir minimum {{ limit }} caractère.',
        maxMessage: 'Le mot de passe ne doit pas dépasser{{ limit }} caractère.',
    )]
    #[Assert\Regex(
            pattern: "/^(?=.*[a-zà-ÿ])(?=.*[0-9])(?=.*[^a-zà-ÿA-Z]).{11,255}$/",
            match: true,
            message: "Le mot de passe doit contenir au moins une lettre minuscule un chiffre et caractère"
    )]
    #[Assert\NotCompromisedPassword(message: "Votre mot de passe est piratable!")]      
    #[ORM\Column]
    private ?string $password = null;


    //   #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;
    
    //    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private Collection $posts;


     public function __construct()
     {
         $this->roles[] = "ROLE_USER";
         $this->posts = new ArrayCollection();
     }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_ROLE';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
     public function setCreatedAt(?\DateTimeImmutable $createdAt): static
     {
         $this->createdAt = $createdAt;

         return $this;
     }
   
    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): static
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
     public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
     {
         $this->updatedAt = $updatedAt;

         return $this;
     }
   

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }
}
