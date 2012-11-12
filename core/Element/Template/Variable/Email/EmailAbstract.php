<?php

namespace ModSync\Element\Template\Variable\Email;

use ModSync\Element\Template\Variable;

abstract class EmailAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_EMAIL;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_EMAIL;
    }

}
