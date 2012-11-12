<?php

namespace ModSync\Element\Template\Variable\Image;

use ModSync\Element\Template\Variable;

abstract class ImageAbstract extends Variable\VariableAbstract {

    protected $_type = self::TYPE_IMAGE;

    /**
     * Returns tv's type
     *
     * @return string
     */
    final public function getType() {
        return self::TYPE_IMAGE;
    }

}
