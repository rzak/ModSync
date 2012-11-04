<?php

namespace ModSync\Element\Template\Variable\Email;

use ModSync\Element\Template\Variable;

abstract class EmailAbstract extends Variable\VariableAbstract {

    protected $_type = 'email';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'email';
    }

}
