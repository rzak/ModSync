<?php

namespace ModSync\Element\Template\Variable\Date;

use ModSync\Element\Template\Variable;

abstract class DateAbstract extends Variable\VariableAbstract {

    protected $_type = 'date';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'date';
    }

}
