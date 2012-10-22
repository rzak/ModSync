<?php

namespace ModSync\Element\Template\Variable\Dropdown;

abstract class YesNoAbstract extends DropdownAbstract {

    final public function getElements() {
        return 'Yes==1||No==0';
    }

    public function getDefaultText() {
        return '1';
    }

}
