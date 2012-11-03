<?php

namespace ModSync\Element\Template;

use ModSync;

interface IsTemplateInterface extends ModSync\Element\IsElementInterface, ModSync\Element\Template\Variable\HasVariableInterface, ModSync\HasContentInterface {

    /**
     * Returns modTemplate object
     *
     * @return \modTemplate
     */
    static public function getModTemplate();
}