<?php

namespace ModSync\Element\Template\Variable\Text;

use ModSync\Element\Template\Variable;

abstract class TextAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_TEXT;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_TEXT;
    }

}
