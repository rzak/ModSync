<?php

namespace ModSync\Element\Template\Variable\Richtext;

use ModSync\Element\Template\Variable;

abstract class RichtextAbstract extends Variable\VariableAbstract {

    protected $_type = 'richtext';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'richtext';
    }

}
