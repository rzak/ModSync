<?php

abstract class ModSync_Template_Abstract extends ModSync_Element implements ModSync_HasId {

    private $_tvs = array();

    /**
     * Sync template object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        self::log('Syncing Template: ' . $this->getName(), Zend_Log::INFO);

        $template = parent::_sync('modTemplate', array('templatename' => $this->getName()));
        $this->assignVariables();
        foreach ($this->getVariables() as $tv) {
            if (!$tvt = self::getModX()->getObject('modTemplateVarTemplate', array('tmplvarid' => $tv->get('id'), 'templateid' => $template->get('id')))) {
                $tvt = self::getModX()->newObject('modTemplateVarTemplate');
                $tvt->set('tmplvarid', $tv->get('id'));
                $tvt->set('templateid', $template->get('id'));
                $tvt->set('rank', $tv->get('rank'));
                $tvt->save();
            }
        }
        unset($template);
    }

    /**
     * This method is called during sync.  It's meant to be extended.
     */
    protected function assignVariables() {
        
    }

    /**
     * Adds a tv to be attached
     *
     * @param string|ModSync_Variable_Abstract $tv
     */
    final public function addVariable($tv) {
        try {
            if (is_string($tv)) {
                if (!is_callable($tv . '::sync')) {
                    throw new ModSync_Exception;
                }
                $tv = new $tv();
            }
            if (!is_a($tv, 'ModSync_Variable_Abstract')) {
                throw new ModSync_Exception;
            }
            $this->_tvs[$tv->getName()] = $tv;
        } catch (Exception $e) {
            throw new ModSync_Exception('Invalid TV: ' . (string) $tv);
        }
    }

    /**
     * Adds a tv to be attached
     *
     * @param string|ModSync_Variable_Abstract $tv
     */
    final public function removeVariable($tv) {
        try {
            if (is_a($tv, 'ModSync_Variable_Abstract')) {
                $tv = $tv->getName();
            }
            if (!isset($this->_tvs[$tv])) {
                throw new ModSync_Exception;
            }
            unset($this->_tvs[$tv]);
        } catch (Exception $e) {
            throw new ModSync_Exception('Invalid TV: ' . (string) $tv);
        }
    }

    /**
     * Adds an array of tvs
     *
     * @param ModSync_Variable_Abstract $tv
     */
    final public function addVariables($tvs = array()) {
        foreach ($tvs as $tv) {
            $this->addVariable($tv);
        }
    }

    /**
     * Returns all attached variables
     *
     * @return array
     */
    final private function getVariables() {
        return $this->_tvs;
    }

    /**
     * Returns modx element's field value
     *
     * @param string $name
     * @return mixed
     */
    final public function get($name) {
        if (!$modxElement = self::getModX()->getObject('modTemplate', array('templatename' => $this->getName()))) {
            $this->sync();
            return $this->get($name);
        }
        return $modxElement->get($name);
    }

    /**
     * Returns the modx element id
     * 
     * @return int
     */
    final public function _getModXId() {
        $modxElement = self::getModX()->getObject('modTemplate', array('templatename' => $this->getName()));
        if (!$modxElement) {
            throw new ModSync_Exception('object is not in modx');
        }
        $id = intval($modxElement->get('id'));
        return $id;
    }

}
