<?php

class ModSync_Parameter_String extends ModSync_Parameter_Abstract {

    public function getValue() {
        return (string) parent::getValue();
    }

}
