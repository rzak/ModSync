<?php

abstract class ModSync_Variable_Image_Abstract extends ModSync_Variable_Abstract {

    protected $_type = 'image';

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return 'image';
    }

}
