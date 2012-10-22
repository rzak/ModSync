<?php

namespace ModSync\Element\Template;

use ModSync;

interface IsTemplateInterface extends ModSync\Element\IsElementInterface, ModSync\Element\Template\Variable\HasVariableInterface {

    /**
     * Returns modTemplate object
     *
     * @return \modTemplate
     */
    public function getModTemplate();
}