<?php

namespace App\Entity;

use App\Repository\PosterRepository;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Mime\Email;

/**
 * @ORM\Entity(repositoryClass=PosterRepository::class)
 */
class Poster //extends Email
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    public function getId(): ?int { return $this->id; }

    public function getText(): ?string { return $this->text; }
    public function setText(string $text): self { $this->text = $text; return $this; }

}
