<?php

abstract class ModSync_Variable_Url_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'url';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'url';
    }

}
