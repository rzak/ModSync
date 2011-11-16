<?php

abstract class ModSync_Variable_Dropdown_YesNoAbstract extends ModSync_Variable_Dropdown_Abstract {

    final public function getElements() {
        return 'Yes==1||No==0';
    }

    public function getDefaultText() {
        return '1';
    }

}
