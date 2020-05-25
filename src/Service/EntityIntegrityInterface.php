<?php


namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

interface EntityIntegrityInterface {
    public function onUpdate(EntityManagerInterface $em,array $context = []);
    public function onRemove(EntityManagerInterface $em);
}

