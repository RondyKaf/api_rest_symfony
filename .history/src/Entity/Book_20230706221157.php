<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("getBooks","getAuthors")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("getBooks","getAuthors")]
    #[Assert\NotBlank(message:"Le titre du livre est obligatoire")]
    #[Assert\NotBlank(min:1,max:255,minMessage:"Le titre doit faire au moins {{ limit}}",maxMessage:"le titre doit faire un moins {{ limit }} ")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups("getBooks","getAuthors")]
    private ?string $coverTitle = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    #[Groups("getBooks")]
    private ?Author $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverTitle(): ?string
    {
        return $this->coverTitle;
    }

    public function setCoverTitle(string $coverTitle): static
    {
        $this->coverTitle = $coverTitle;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}
