<?php

namespace ModSync\Element\Template\Variable;

use ModSync;

interface IsVariableInterface extends ModSync\Element\IsElementInterface {

    /**
     * Returns caption
     *
     * @return string
     */
    public function getCaption();

    /**
     * Returns tv's type
     *
     * @return string
     */
    public function getType();

    /**
     * Returns \modTemplateVar object
     *
     * @return \modTemplateVar
     */
    static public function getModTemplateVar();
}