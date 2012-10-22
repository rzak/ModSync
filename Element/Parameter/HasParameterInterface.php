<?php

namespace ModSync\Element\Parameter;

use ModSync;

interface HasParameterInterface {

    /**
     * Define parameters 
     */
    public function defineParams();

    /**
     * Adds a parameter to snippet
     *
     * @param ModSync\Element\Parameter\ParameterAbstract $param
     */
    public function addParam(ModSync\Element\Parameter\ParameterAbstract $param);

    /**
     * Replaces a parameter in snippet
     *
     * @param ModSync\Element\Parameter\ParameterAbstract $param
     */
    public function replaceParam(ModSync\Element\Parameter\ParameterAbstract $param);

    /**
     * Removes a parameter from snippet
     *
     * @param string $name
     */
    public function removeParam($name);

    /**
     * Returns a snippet parameter
     *
     * @param string $name
     * @return ModSync\Element\Parameter\ParameterAbstract
     */
    public function getParam($name);

    /**
     * Returns a list of parameters
     *
     * @return array
     */
    public function getParams();

    /**
     * Return property
     * 
     * @return mixed 
     */
    public function getProp($name, $default = null);
}