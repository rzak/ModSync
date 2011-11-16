<?php

abstract class ModSync_Variable_Richtext_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'richtext';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'richtext';
    }

}
