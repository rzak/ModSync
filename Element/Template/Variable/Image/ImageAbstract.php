<?php

namespace ModSync\Element\Template\Variable\Image;

use ModSync\Element\Template\Variable;

abstract class ImageAbstract extends Variable\VariableAbstract {

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
