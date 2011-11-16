<?php

abstract class ModSync_Variable_Dropdown_EnableAbstract extends ModSync_Variable_Dropdown_Abstract {
    const ENABLE = 'enable';
    const DISABLE = 'disable';

    final public function getElements() {
        return sprintf('Enable==%s||Disable==%s', self::ENABLE, self::DISABLE);
    }

    public function getDefaultText() {
        return self::ENABLE;
    }

}
