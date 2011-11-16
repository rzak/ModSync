<?php

abstract class ModSync_Variable_Checkbox_Abstract extends ModSync_Variable_Abstract {

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
