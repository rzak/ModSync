<?php

namespace ModSync\Element\Template\Variable\Textarea;

use ModSync\Element\Template\Variable;

abstract class TextareaAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_TEXTAREA;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_TEXTAREA;
    }

}
