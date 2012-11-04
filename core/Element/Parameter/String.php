<?php

namespace ModSync\Element\Parameter;

use ModSync;

class String extends ModSync\Element\Parameter\ParameterAbstract {

    public function getValue() {
        return (string) parent::getValue();
    }

}
