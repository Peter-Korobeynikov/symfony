<?php

namespace App\Entity;

use App\Common\TProductManager;
use App\Repository\ProductRepository;
use App\Service\EntityIntegrityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use JMS\Serializer\SerializerBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validation;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product implements EntityIntegrityInterface
{
    use TProductManager;

    /**
     * The unique auto incremented primary key
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $id;

    /**
     * Many Products have Many Categories.
     * @var ArrayCollection
     * @ManyToMany(targetEntity="Category")
     * @JoinTable(name="products_categories",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;

    /**
     * @var string
     * @ORM\Column(type="string", length=12, nullable=false)
     * @Assert\Length(min=3,  minMessage="The title must be at least 3 characters long")
     * @Assert\Length(max=12, maxMessage="the maximum length of the title is 12 characters")
     */
    private $title;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      max = 200,
     *      minMessage = "The price must be positive",
     *      maxMessage = "The price must not exceed 200"
     * )
     */
    private $price;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eId;

    public function __construct() {
        $this->id = 0;
        $this->setTitle('');
        $this->setPrice(0);
        $this->setEid(0);
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }

    public function getEId(): ?int { return $this->eId; }
    public function setEId(?int $eId): self { $this->eId = $eId; return $this; }

    /**
     * @return ArrayCollection
     */
    public function getCategories() { return $this->categories; }
    public function setCategories($categories): self { $this->categories = $categories; return $this; }

    public function addCategory(Category $category): self {
        if (!$this->categories->contains($category)) { $this->categories[] = $category; }
        return $this;
    }

    public function removeCategory(Category $category) {
        $this->categories->removeElement($category);
    }

    //------------------- EntityIntegrityInterface
    public function json_serialize(): string {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($this, 'json');
    }

    public function json_deserialize(EntityManagerInterface $em, $json_data): object {
        // Убеждаемся, что у нас ассоциативный массив свойств ('eId' - обязательное)
        if (!is_array($json_data)) $json_data = json_decode(json_encode($json_data),true);
        if (!array_key_exists('eId', $json_data)) return null;

        // Проверяем наличие элемента в базе с тем же ключом 'eId'
        $product = $em->getRepository(Product::class)->findOneBy(['eId' => $json_data['eId']]);
        if (!isset($product) || empty($product)) $product = $this;

        // Раскладываем значения по полочкам
        foreach ($json_data as $key => $value) {
            switch ($key) {
                case 'eId':             $product->setEId($value); break;
                case 'title':           $product->setTitle($value); break;
                case 'price':           $product->setPrice($value); break;
                case 'categoryEId':
//                    $data = json_decode(json_encode($value),true);
//                    foreach ($data as $value) {
//                        $category = $em->getRepository(Category::class)->findOneBy(['eId' => $value]);
//                        if (isset($category) && !empty($category)) {
//                            $product->addCategory($category);
//                        }
//                    }
                    break;
            }
        }
        return $product;
    }

    public function onUpdate(EntityManagerInterface $em,array $context = []): bool {
        // Вызываем метод Doctrine для валидации
        $validator = Validation::createValidator();
        $errors = $validator->validate($this);
        if (count($errors) > 0) return false;
        return true;
    }

    public function onRemove(EntityManagerInterface $em): bool {
        return true;
    }

}
