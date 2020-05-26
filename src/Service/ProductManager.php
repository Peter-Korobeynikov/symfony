<?php

namespace App\Service;

use App\Common\TEntityManager;
//use App\Entity\Category;
//use App\Service\EntityIntegrityInterface;
//use JMS\Serializer\SerializerBuilder;
//use Symfony\Component\Validator\Validation;

// ProductManager service
class ProductManager
{
    use TEntityManager;    // Wiring service

    /**
     * @var EntityIntegrityInterface|null
     * @var object|null
     */
    private $_inst;

    public function __construct()       { $this->_inst = null; }
    public function __destruct()        {}

    public function attached(): bool    { return (isset($this->_inst)); }
    public function is_ready(): bool    { return (isset($this->_em) && $this->attached()); }

    public function className(): string { return get_class($this->_inst); }
    public function repository($class)  { return $this->_em->getRepository($class); }

    public function instance(): ?object { return $this->_inst; }
    public function integrity(): ?EntityIntegrityInterface { return $this->_inst; }

    public function link($obj): self    { return $this->attach(get_class($obj), $obj); }
    public function detach(): void      { $this->_inst = null; }

    public function attach(string $class, $ent = null): self {
        if (!isset($ent)) $ent = new $class();  // Create new instance оf entity class
        $this->_inst = $ent;                    // .. or attach existing
        assert($this->is_ready());
        return $this;
    }

    public function find(string $class, array $criteria, array $orderBy = null, $limit = 1, $offset = null) {
        return $this->repository($class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    // -------------------------
    public function serialize(): string { return ''; }
    public function deserialize($json_data): self { return $this; }

    // -------------------------
    public function import(string $className, string $fileName){
        $json_file = file_get_contents($fileName);
        $json_data = json_decode($json_file, true);

        foreach($json_data as $item) {
            $this->attach($className);     // Новый инстанс класса
            $this->update($item);          // Идем обновлять данные в базе
        }
    }

    // -------------------------
    public function update($json_data = null, array $context = []) {
        if (!$this->is_ready()) return null;

        // Вызываем метод объекта для сериализации
        if (isset($json_data)) $this->link($this->integrity()->json_deserialize($this->_em, $json_data));
        if (!$this->is_ready()) return null;

        // Вызываем метод объекта для валидации
        $is_valid = $this->integrity()->onUpdate($this->_em, $context);

        // Обновляем базу
        if ($is_valid) {
            try {
                $this->_em->persist($this->instance());
                $this->_em->flush();
            } catch (\Exception $exception) {}
        }
    }

    // -------------------------
    public function remove(): void {
        assert($this->is_ready(), "$this - Self validity check");

        // Вызываем метод объекта
        $this->integrity()->onRemove($this->_em);

        // Удаляем из базы
        $this->_em->remove($this->_inst);
        $this->_em->flush();
        $this->detach();
    }

}