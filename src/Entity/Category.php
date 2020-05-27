<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Service\EntityIntegrityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category implements EntityIntegrityInterface //, \JsonSerializable
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
     * @Assert\Length(
     *     min=3, max=12,
     *     minMessage="The title must be at least 3 characters long",
     *     maxMessage="the maximum length of the title is 12 characters")
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

    protected function _serialize() {
        return [ 'id' => $this->id, 'title' => $this->title, 'eId' => $this->eId ];
    }
    public function __toString(): string {
        $str = implode(', ',$this->_serialize());
        return (string) $str;
    }

    //------------------- EntityIntegrityInterface
    public function json_serialize(): string {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($this, 'json');
    }

    public function json_deserialize(EntityManagerInterface $em, $json_data): object {
        // Убеждаемся, что у нас ассоциативный массив свойств ('eId' - обязательное)
        if (!is_array($json_data)) $json_data = json_decode($json_data,true);
        if (!array_key_exists('eId', $json_data)) return null;

        // Проверяем наличие элемента в базе с тем же ключом 'eId'
        $category = $em->getRepository(Category::class)->findOneBy(['eId' => $json_data['eId']]);
        if (!isset($category) || empty($category)) $category = $this;

        // Раскладываем значения по полочкам
        foreach ($json_data as $key => $value) {
            switch ($key) {
                case 'eId':     $category->setEId($value); break;
                case 'title':   $category->setTitle($value); break;
                case 'content': $category->setContent($value); break;
            }
        }
        return $category;
    }

    public function onUpdate(EntityManagerInterface $em, array $context = []): bool {
        // Вызываем метод Doctrine для валидации
        $validator = Validation::createValidator();
        $errors = $validator->validate($this);
        if (count($errors) > 0) return false;
        return true;
    }

    public function onRemove(EntityManagerInterface $em): bool {
        // При удалении категории - удалим её из коллекций продуктов
        $repository = $em->getRepository(Product::class);
        $products = $repository->findAll();
        foreach ($products as $product) $product->removeCategory($this);
        return true;
    }
}
