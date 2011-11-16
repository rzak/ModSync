<?php

abstract class ModSync_Variable_ListboxMultiple_Abstract extends ModSync_Variable_Abstract {

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
