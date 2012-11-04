<?php

namespace ModSync\Element\Template\Variable\Text;

use ModSync\Element\Template\Variable;

abstract class TextAbstract extends Variable\VariableAbstract {

    protected $_type = 'text';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'text';
    }

}
