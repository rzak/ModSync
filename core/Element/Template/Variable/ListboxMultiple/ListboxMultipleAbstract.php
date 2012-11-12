<?php

namespace ModSync\Element\Template\Variable\ListboxMultiple;

use ModSync\Element\Template\Variable;

abstract class ListboxMultipleAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_LISTBOX_MULTIPLE;

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
        return self::TYPE_LISTBOX_MULTIPLE;
    }

    abstract public function getElements();
}
