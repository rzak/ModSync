<?php

abstract class ModSync_Snippet_Abstract extends ModSync_Element {

    protected $_scriptProperties;
    protected $_params = array();
    protected $_output = null;

    public function __construct($scriptProperties = array()) {
        parent::__construct();
        $this->_scriptProperties = (array) $scriptProperties;
    }

    abstract protected function defineParams();

    abstract protected function dispatch();

    protected function preDispatch() {
        
    }

    protected function postDispatch() {
        
    }

    /**
     * This is where the magic happens... modx snippet should always call this
     * method to execute the snippet.
     *
     * @return string
     */
    final public function run() {
        try {
            $tstart = explode(' ', microtime());
            $tstart = $tstart[1] + $tstart[0];



            $this->defineParams();
            /* @var $param ModSync_Parameter_Abstract */
            foreach ($this->getParams() as $param) {
                if (isset($this->_scriptProperties[strtolower($param->getName())]) && !empty($this->_scriptProperties[strtolower($param->getName())])) {
                    $param->setValue($this->_scriptProperties[strtolower($param->getName())]);
                }
            }
            $this->preDispatch();
            $this->_output = $this->dispatch();
            $this->postDispatch();


            $tstop = explode(' ', microtime());
            $tstop = $tstop[1] + $tstop[0];
            $duration = $tstop - $tstart;
            self::log(sprintf('Snippet: %s (%1.3f seconds)', $this->getName(), $duration), Zend_Log::DEBUG);
            if ($duration > 1) {
                self::log(sprintf('Inefficient Snippet Found: %s (%1.3f seconds)', $this->getName(), $duration), Zend_Log::WARN);
            }
            return $this->_output;
        } catch (Exception $e) {
            $msg = 'Snippet (' . $this->getName() . ') failed: ' . $e->getMessage();
            self::log($msg, Zend_Log::ERR);
            die($msg);
        }
    }

    /**
     * Adds a parameter to snippet
     *
     * @param ModSync_Parameter_Abstract $param
     */
    final protected function addParam(ModSync_Parameter_Abstract $param) {
        if (array_key_exists($param->getName(), $this->_params)) {
            throw new ModSync_Exception($this, 'Parameter (' . $param->getName() . ') already exists');
        }
        $this->_params[$param->getName()] = $param;
    }

    /**
     * Replaces a parameter in snippet
     *
     * @param ModSync_Parameter_Abstract $param
     */
    final protected function replaceParam(ModSync_Parameter_Abstract $param) {
        if (!array_key_exists($param->getName(), $this->_params)) {
            throw new ModSync_Exception($this, 'Parameter (' . $param->getName() . ') cannot be replaced');
        }
        unset($this->_params[$param->getName()]);
        $this->_params[$param->getName()] = $param;
    }

    /**
     * Removes a parameter from snippet
     *
     * @param string $name
     */
    final protected function removeParam($name) {
        if (!array_key_exists($name, $this->_params)) {
            throw new ModSync_Exception($this, 'Parameter (' . $name . ') does not exist');
        }
        unset($this->_params[$name]);
    }

    /**
     * Returns a snippet parameter
     *
     * @param string $name
     * @return ModSync_Parameter_Abstract
     */
    final public function getParam($name) {
        if (!array_key_exists($name, $this->_params)) {
            throw new ModSync_Exception('Parameter (' . $name . ') does not exist');
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
     * Returns the snippet content
     * 
     * @return string
     */
    public function getContent() {
        $content = '
<?php
$o = new ' . get_class($this) . '($scriptProperties);
return $o->run();
';
        return $content;
        ;
    }

    /**
     * Sync snippet object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        self::log('Syncing Snippet: ' . $this->getName(), Zend_Log::INFO);

        $snippet = parent::_sync('modSnippet', array('name' => $this->getName()));
        unset($snippet);
    }

}
