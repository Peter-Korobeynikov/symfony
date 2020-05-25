<?php

namespace App\Service;

use App\Common\TEntityManager;
use App\Service\EntityIntegrityInterface;
use JMS\Serializer\SerializerBuilder;

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
    public function repository()        { return $this->_em->getRepository($this->className()); }

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

    public function find(array $criteria, array $orderBy = null, $limit = 1, $offset = null) {
        return $this->repository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    // -------------------------
    public function serialize(): string {
        assert($this->is_ready());
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($this->instance(), 'json');
    }

    public function deserialize($json_data): self {
        $serializer = SerializerBuilder::create()->build();
        $ent = $serializer->deserialize($json_data, $this->className(), 'json');
        return $this->attach($ent);
    }

    // -------------------------
    public function update($json_data = null, array $context = []) {
        if (!$this->is_ready()) return null;
        if (isset($json_data)) $this->deserialize($json_data); // Set entity data (deserialize)

        // Вызываем метод объекта
        $this->integrity()->onUpdate($this->_em, $context);

        // Обновляем базу
        $this->_em->persist($this->instance());
        $this->_em->flush();
    }

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