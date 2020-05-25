<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Service\EntityIntegrityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category implements EntityIntegrityInterface, \JsonSerializable
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    public function __construct() {
        $this->id = 0;
        $this->eId = 0;
        $this->title = '';
        $this->content = "";
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getEId(): ?int { return $this->eId; }
    public function setEId(?int $eId): self { $this->eId = $eId; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function __toString(): string {
        $str = implode(', ',$this->jsonSerialize());
        return (string) $str;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'eId' => $this->eId,
            'content' => $this->content
        ];
    }

    //------------------- EntityIntegrityInterface
    public function onUpdate(EntityManagerInterface $em,array $context = []) {
        echo '*** onUpdate Category';
    }

    public function onRemove(EntityManagerInterface $em) {
        echo '*** onRemove Category';
        // При удалении категории - удалим её из коллекций продуктов
        $repository = $em->getRepository(Product::class);
        $products = $repository->findAll();
        foreach ($products as $product) {
            $product->removeCategory($this);
        }
    }
}
