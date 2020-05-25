<?php


namespace App\Common;

use Doctrine\ORM\EntityManagerInterface;

trait TEntityManager {
    /**
     * @var EntityManagerInterface|null
     */
    private $_em;
    /**
     * @param  mixed $em
     * @required
     */
    public function setEM(EntityManagerInterface $em): self { $this->_em = $em; return $this; }
    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEM() { return $this->_em; }
}