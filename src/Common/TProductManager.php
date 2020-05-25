<?php

namespace App\Common;

use App\Service\ProductManager;

trait TProductManager {
    /**
     * @var ProductManager|null
     */
    private $_pm;
    /**
     * @param  mixed $pm
     * @required
     */
    public function setPM(ProductManager $pm): self { $this->_pm = $pm; return $this; }
    /**
     * @return \App\Service\ProductManager
     */
    public function getPM() { return $this->_pm; }
}
