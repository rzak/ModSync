<?php

abstract class ModSync_Variable_Hidden_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'hidden';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'hidden';
    }

}
