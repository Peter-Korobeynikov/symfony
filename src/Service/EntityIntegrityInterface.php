<?php


namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

interface EntityIntegrityInterface {
    public function json_serialize(): string;
    public function json_deserialize(EntityManagerInterface $em, $json_data): object;
    public function onUpdate(EntityManagerInterface $em, array $context = []): bool;
    public function onRemove(EntityManagerInterface $em): bool;
}

