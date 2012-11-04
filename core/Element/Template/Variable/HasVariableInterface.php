<?php

namespace ModSync\Element\Template\Variable;

use ModSync;

interface HasVariableInterface {

    /**
     * Adds a tv to be attached
     *
     * @param string|ModSync\Element\Template\Variable\VariableAbstract $tv
     */
    public function addVariable($tv);

    /**
     * Adds an array of tvs
     *
     * @param array $tvs
     */
    public function addVariables($tvs = array());

    /**
     * Adds a tv to be attached
     *
     * @param string|ModSync\Element\Template\Variable\VariableAbstract $tv
     */
    public function removeVariable($tv);
}