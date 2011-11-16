<?php

class ModSync_Parameter_Int extends ModSync_Parameter_Abstract {

    public function getValue() {
        return intval(parent::getValue());
    }

}
