<?php

namespace ModSync\Element\Template\Variable\Url;

use ModSync\Element\Template\Variable;

abstract class UrlAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_URL;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_URL;
    }

}
