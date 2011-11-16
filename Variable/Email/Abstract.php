<?php

abstract class ModSync_Variable_Email_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'email';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'email';
    }

}
