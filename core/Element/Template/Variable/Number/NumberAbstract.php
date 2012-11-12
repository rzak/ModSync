<?php

namespace ModSync\Element\Template\Variable\Number;

use ModSync\Element\Template\Variable;

abstract class NumberAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_NUMBER;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_NUMBER;
    }

}
