<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("getBooks")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("getBooks")]

    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups("getBooks")]
    private ?string $coverTitle = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
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
