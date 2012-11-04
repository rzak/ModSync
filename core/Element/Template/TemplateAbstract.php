<?php

namespace ModSync\Element\Template;

use ModSync;

abstract class TemplateAbstract extends ModSync\Element\ElementAbstract implements ModSync\Element\Template\IsTemplateInterface {

    private $_tvs = array();

    /**
     * Sync template object
     */
    final public function sync() {
        $modTemplate = parent::_sync('modTemplate', array('templatename' => $this->getName()));
        $this->assignVariables();
        foreach ($this->getVariables() as $tv) {
            if (!$tvt = self::getModX()->getObject('modTemplateVarTemplate', array('tmplvarid' => $tv->get('id'), 'templateid' => $modTemplate->get('id')))) {
                $tvt = self::getModX()->newObject('modTemplateVarTemplate');
                $tvt->set('tmplvarid', $tv->get('id'));
                $tvt->set('templateid', $modTemplate->get('id'));
                $tvt->set('rank', $tv->get('rank'));
                $tvt->save();
            }
        }
    }

    /**
     * This method is called during sync.  It's meant to be extended.
     */
    protected function assignVariables() {
        
    }

    /**
     * Adds a tv to be attached
     *
     * @param string|ModSync\Element\Template\Variable\VariableAbstract $tv
     */
    final public function addVariable($tv) {
        try {
            if (is_string($tv)) {
                if (!is_callable($tv . '::sync')) {
                    throw new Exception;
                }
                $tv = new $tv();
            }
            if (!is_a($tv, 'ModSync\Element\Template\Variable\IsVariableInterface')) {
                throw new Exception;
            }
            $this->_tvs[$tv->getName()] = $tv;
        } catch (Exception $e) {
            throw new Exception('Invalid TV: ' . (string) $tv);
        }
    }

    /**
     * Adds an array of tvs
     *
     * @param array $tvs
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
     * Adds a tv to be attached
     *
     * @param string|ModSync\Element\Template\Variable\VariableAbstract $tv
     */
    final public function removeVariable($tv) {
        try {
            if (is_a($tv, 'ModSync\Element\Template\Variable\VariableAbstract')) {
                $tv = $tv->getName();
            }
            if (!isset($this->_tvs[$tv])) {
                throw new ModSync\Element\Template\Exception;
            }
            unset($this->_tvs[$tv]);
        } catch (ModSync\Exception $e) {
            throw new ModSync\Exception('Invalid TV: ' . (string) $tv);
        }
    }

    /**
     * Returns modTemplate object
     *
     * @return \modTemplate
     */
    final static public function getModTemplate() {
        $class = get_called_class();
        $o = new $class();
        $modTemplate = self::getModX()->getObject('modTemplate', array('templatename' => $o->getName()));
        if (!$modTemplate) {
            $o->sync();
            return $o->getModTemplate();
        }
        return $modTemplate;
    }

}
