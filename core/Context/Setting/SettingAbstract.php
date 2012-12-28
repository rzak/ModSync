<?php

namespace ModSync\Context\Setting;

use ModSync;

abstract class SettingAbstract extends ModSync\System\Setting\SettingAbstract {

    protected $_context_key;

    /**
     * Returns element's key
     *
     * @return string
     */
    public function getKey() {
        if (null === $this->_key) {
            $chunks = explode('_', str_replace('\\', '_', get_class($this)), 5);
            $this->_key = strtolower($chunks[0] . '__' . $chunks[4]);
        }
        return strtolower($this->_key);
    }

    /**
     * Returns element's value
     *
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Returns element's context key
     *
     * @return string
     */
    final public function getContextKey() {
        if (null === $this->_context_key) {
            $chunks = explode('_', str_replace('\\', '_', get_class($this)), 5);
            $this->_context_key = strtolower($chunks[3]);
        }
        return strtolower($this->_context_key);
    }

    /**
     * Sync context object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }
        /* @var $modxElement \modContextSetting */
        $modxElement = self::getModX()->getObject('modContextSetting', array('context_key' => $this->getContextKey(), 'key' => $this->getKey(), 'namespace' => $this->getNamespace()));
        if ($modxElement) {
            ModSync\Logger::debug('Already exists: ' . get_called_class());
        } else {
            ModSync\Logger::info('Inserting: ' . get_called_class());
            $modxElement = self::getModX()->newObject('modContextSetting');
            $modxElement->set('context_key', strtolower($this->getContextKey()));
            $modxElement->set('key', strtolower($this->getKey()));
            $modxElement->set('namespace', $this->getNamespace());
            $modxElement->set('value', $this->getValue());
            $modxElement->set('xtype', $this->_xtype);
            $modxElement->set('area', $this->getArea());
            $this->onInsert();
            $modxElement->save();
        }
    }

}