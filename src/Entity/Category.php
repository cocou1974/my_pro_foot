<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('name', message:"Cette categorie existe déjà")]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

   
    // #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    
    // #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }



     public function setSlug(string $slug): static
     {
         $this->slug = $slug;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }


    /**
     * @return array
     */
    public function getPublishedPosts(): array
    {
        $posts = $this->posts->toArray();

        $postsPublished = [];

        foreach ($posts as $post)
        {
            if ( $post->isIspublished() )
            {
                $postsPublished [] = $post;
            }
        }

        return $postsPublished;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }
}









// #[Gedmo\Slug(fields: ['name'])]
//     #[ORM\Column(length: 255, unique: true)]
//     private ?string $slug = null;


//     #[Gedmo\Timestampable(on: 'create')]
//     #[ORM\Column(nullable: true)]
//     private ?\DateTimeImmutable $createdAt = null;


//     #[Gedmo\Timestampable(on: 'update')]
//     #[ORM\Column(nullable: true)]
//     private ?\DateTimeImmutable $updatedAt = null;

//     #[ORM\OneToMany(mappedBy: 'category', targetEntity: Post::class, orphanRemoval: true)]
//     private Collection $posts;

//     public function __construct()
//     {
//         $this->posts = new ArrayCollection();
//     }



//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getName(): ?string
//     {
//         return $this->name;
//     }

//     public function setName(?string $name): static
//     {
//         $this->name = $name;

//         return $this;
//     }

//     public function getSlug(): ?string
//     {
//         return $this->slug;
//     }

//     public function getCreatedAt(): ?\DateTimeImmutable
//     {
//         return $this->createdAt;
//     }

//     public function getUpdatedAt(): ?\DateTimeImmutable
//     {
//         return $this->updatedAt;
//     }

//     /**
//      * @return Collection<int, Post>
//      */
//     public function getPosts(): Collection
//     {
//         return $this->posts;
//     }

//     public function addPost(Post $post): static
//     {
//         if (!$this->posts->contains($post)) {
//             $this->posts->add($post);
//             $post->setCategory($this);
//         }

//         return $this;
//     }

//     public function removePost(Post $post): static
//     {
//         if ($this->posts->removeElement($post)) {
//             // set the owning side to null (unless already changed)
//             if ($post->getCategory() === $this) {
//                 $post->setCategory(null);
//             }
//         }

//         return $this;
//     }

// }
