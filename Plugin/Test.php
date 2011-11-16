<?php

class ModSync_Plugin_Test extends ModSync_Plugin_Abstract
{

    protected $_syncable = false;

    protected function getMethods()
    {
        $methods = array();
        foreach ($this->_available_events as $event)
        {
            array_push($methods, self::EVENT_METHOD_PREFIX . $event);
        }
        return $methods;
    }

    public function __call($name, $arguments)
    {
        $this->_myTest($name);
    }

    private function _myTest($event)
    {
    }

}