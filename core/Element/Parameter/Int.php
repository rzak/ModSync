<?php

namespace ModSync\Element\Parameter;

use ModSync;

class Int extends ModSync\Element\Parameter\ParameterAbstract {

    public function getValue() {
        return intval(parent::getValue());
    }

}
