<?php

namespace ModSync\Element\Template\Variable\Dropdown;

abstract class EnableAbstract extends DropdownAbstract {
    const ENABLE = 'enable';
    const DISABLE = 'disable';

    final public function getElements() {
        return sprintf('Enable==%s||Disable==%s', self::ENABLE, self::DISABLE);
    }

    public function getDefaultText() {
        return self::ENABLE;
    }

}
