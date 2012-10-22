<?php

namespace ModSync\Element\Template\Variable\ListboxMultiple;

use ModSync\Element\Template\Variable;

abstract class ListboxMultipleAbstract extends Variable\VariableAbstract {

    protected $_type = 'listbox-multiple';

    public function __construct() {
        parent::__construct();
        $this->_elements = $this->getElements();
    }

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'listbox-multiple';
    }

    abstract public function getElements();
}
