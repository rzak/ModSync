<?php

abstract class ModSync_Variable_Text_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'text';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'text';
    }

}
