<?php

abstract class ModSync_Variable_Date_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'date';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'date';
    }

}
