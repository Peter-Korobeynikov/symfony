<?php

namespace App\Entity;

use App\Repository\CategoryRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * The unique auto incremented primary key
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=12, nullable=false)
     * @Assert\Length(min=3,  minMessage="The title must be at least 3 characters long")
     * @Assert\Length(max=12, maxMessage="the maximum length of the title is 12 characters")
     */
    private $title;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eId;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getEId(): ?int { return $this->eId; }
    public function setEId(?int $eId): self { $this->eId = $eId; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

}
