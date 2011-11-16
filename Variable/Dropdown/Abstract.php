<?php

abstract class ModSync_Variable_Dropdown_Abstract extends ModSync_Variable_Abstract {

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
