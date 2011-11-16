<?php

abstract class ModSync_Variable_Textarea_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'textarea';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'textarea';
    }

}
