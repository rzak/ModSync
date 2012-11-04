<?php

namespace ModSync\Element\Snippet;

use ModSync;

abstract class SnippetAbstract extends ModSync\Element\ElementAbstract implements ModSync\Element\Snippet\IsSnippetInterface {

    use ModSync\Element\HasProfilingTrait;

    protected $_args;
    protected $_params = array();
    protected $_output = null;

    public function __construct($args = array()) {
        parent::__construct();
        $this->_args = $args;
    }

    /**
     * This is where the magic happens... modx snippet should always call this
     * method to execute the snippet.
     *
     * @return string
     */
    final static public function process($args) {
        $class = get_called_class();
        $o = new $class($args);
        return $o->run();
    }

    /**
     * This is where the magic happens... modx snippet should always call this
     * method to execute the snippet.
     *
     * @return string
     */
    final public function run() {
        try {
            $this->beginProfiling();
            $this->defineParams();
            /* @var $param ModSync\Element\Parameter\ParameterAbstract */
            foreach ($this->getParams() as $param) {
                if (isset($this->_args['properties'][strtolower($param->getName())]) && !empty($this->_args['properties'][strtolower($param->getName())])) {
                    $param->setValue($this->_args['properties'][strtolower($param->getName())]);
                } else {
                    if ($param->isRequired()) {
                        throw new ModSync\Element\Parameter\Exception('Parameter (' . $param->getName() . ') is required');
                    }
                }
            }
            $this->beforeDispatch();
            $this->_output = $this->dispatch();
            $this->afterDispatch();
            $this->reportProfiling();
            return $this->_output;
        } catch (ModSync\Exception $e) {
            $msg = 'Snippet (' . $this->getName() . ') failed: ' . $e->getMessage();
            ModSync\Logger::error($msg);
            die($msg);
        }
    }

    abstract public function dispatch();

    /**
     * Define Parameters here 
     */
    public function beforeDispatch() {
        
    }

    /**
     * Define Parameters here 
     */
    public function afterDispatch() {
        
    }

    /**
     * Define Parameters here 
     */
    public function defineParams() {
        
    }

    /**
     * Adds a parameter to snippet
     *
     * @param ModSync\Element\Parameter\ParameterAbstract $param
     */
    final public function addParam(ModSync\Element\Parameter\ParameterAbstract $param) {
        if (array_key_exists($param->getName(), $this->_params)) {
            throw new ModSync\Element\Parameter\Exception('Parameter (' . $param->getName() . ') already exists');
        }
        $this->_params[$param->getName()] = $param;
    }

    /**
     * Replaces a parameter in snippet
     *
     * @param ModSync\Element\Parameter\ParameterAbstract $param
     */
    final public function replaceParam(ModSync\Element\Parameter\ParameterAbstract $param) {
        if (!array_key_exists($param->getName(), $this->_params)) {
            throw new ModSync\Element\Parameter\Exception($this, 'Parameter (' . $param->getName() . ') cannot be replaced');
        }
        unset($this->_params[$param->getName()]);
        $this->_params[$param->getName()] = $param;
    }

    /**
     * Removes a parameter from snippet
     *
     * @param string $name
     */
    final public function removeParam($name) {
        if (!array_key_exists($name, $this->_params)) {
            throw new ModSync\Element\Parameter\Exception($this, 'Parameter (' . $name . ') does not exist');
        }
        unset($this->_params[$name]);
    }

    /**
     * Returns a snippet parameter
     *
     * @param string $name
     * @return ModSync\Element\Parameter\ParameterAbstract
     */
    final public function getParam($name) {
        if (!array_key_exists($name, $this->_params)) {
            throw new ModSync\Element\Parameter\Exception('Parameter (' . $name . ') does not exist');
        }
        return $this->_params[$name];
    }

    /**
     * Returns a list of parameters
     *
     * @return array
     */
    final public function getParams() {
        return $this->_params;
    }

    /**
     * Get Property
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    final public function getProp($name, $default = null) {
        if (isset($this->_args['properties'][$name])) {
            return $this->_args['properties'][$name];
        }
        return $default;
    }

    /**
     * Returns the snippet content
     * 
     * @return string
     */
    public function getContent() {
        $content = '<?php
return ' . get_class($this) . '::process(array("properties" => $scriptProperties));
';
        return $content;
    }

    /**
     * Sync snippet object
     */
    final public function sync() {
        $this->_sync('modSnippet', array('name' => $this->getName()));
    }

}
