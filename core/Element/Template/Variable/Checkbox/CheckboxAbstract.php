<?php

namespace ModSync\Element\Template\Variable\Checkbox;

use ModSync\Element\Template\Variable;

abstract class CheckboxAbstract extends Variable\VariableAbstract {

    protected $_type = 'checkbox';

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
        return 'checkbox';
    }

    abstract public function getElements();

    abstract public function getDefaultText();
}
