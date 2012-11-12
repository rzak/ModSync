<?php

namespace ModSync\Element\Template\Variable\Richtext;

use ModSync\Element\Template\Variable;

abstract class RichtextAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_RICHTEXT;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_RICHTEXT;
    }

}
