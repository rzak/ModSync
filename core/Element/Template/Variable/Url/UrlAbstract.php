<?php

namespace ModSync\Element\Template\Variable\Url;

use ModSync\Element\Template\Variable;

abstract class UrlAbstract extends Variable\VariableAbstract {

    protected $_type = 'url';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'url';
    }

}
