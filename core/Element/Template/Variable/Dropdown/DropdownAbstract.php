<?php

namespace ModSync\Element\Template\Variable\Dropdown;

use ModSync\Element\Template\Variable;

abstract class DropdownAbstract extends Variable\VariableAbstract {

    protected $_type = 'listbox';

    public function __construct() {
        parent::__construct();
        $this->_elements = $this->getElements();
        $this->_default_text = $this->getDefaultText();
    }

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'listbox';
    }

    abstract public function getElements();

    abstract public function getDefaultText();
}
