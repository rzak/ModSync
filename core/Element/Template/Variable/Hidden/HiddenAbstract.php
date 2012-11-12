<?php

namespace ModSync\Element\Template\Variable\Hidden;

use ModSync\Element\Template\Variable;

abstract class HiddenAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_HIDDEN;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_HIDDEN;
    }

}
