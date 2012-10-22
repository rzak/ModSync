<?php

namespace ModSync\Element\Template\Variable\Hidden;

use ModSync\Element\Template\Variable;

abstract class HiddenAbstract extends Variable\VariableAbstract {

    protected $_type = 'hidden';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'hidden';
    }

}
