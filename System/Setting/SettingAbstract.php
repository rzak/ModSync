<?php

namespace ModSync\System\Setting;

use ModSync;

abstract class SettingAbstract extends ModSync\Base implements ModSync\System\Setting\IsSystemSettingInterface {

    const TYPE_TEXTFIELD = 'textfield';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_YESNO = 'combo-boolean';
    const TYPE_PASSWORD = 'text-password';
    const TYPE_CATEGORY = 'modx-combo-category';
    const TYPE_CHARSET = 'modx-combo-charset';
    const TYPE_COUNTRY = 'modx-combo-country';
    const TYPE_CONTEXT = 'modx-combo-context';
    const TYPE_NAMESPACE = 'modx-combo-namespace';
    const TYPE_TEMPLATE = 'modx-combo-template';
    const TYPE_USER = 'modx-combo-user';
    const TYPE_USERGROUP = 'modx-combo-usergroup';
    const TYPE_LANGUAGE = 'modx-combo-language';

    protected $_syncable = true;
    protected $_key;
    protected $_value;
    protected $_xtype = self::TYPE_TEXTFIELD;
    protected $_area;

    /**
     * Sets property
     * 
     * @param string $key
     * @param mixed $val
     * @return SettingAbstract
     */
    public function set($key, $val) {
        $f = '_' . $key;
        $this->$f = $val;
        return $this;
    }

    /**
     * Returns element's key
     *
     * @return string
     */
    final public function getKey() {
        if (null === $this->_key) {
            $chunks = explode('_', str_replace('\\', '_', get_class($this)), 4);
            $this->_key = strtolower($chunks[0] . '__' . $chunks[3]);
        }
        return strtolower($this->_key);
    }

    /**
     * Returns element's key
     *
     * @return string
     */
    final public function getArea() {
        if (null === $this->_area) {
            $chunks = explode('_', str_replace('\\', '_', get_class($this)));
            if (count($chunks) <= 4) {
                $this->_area = 'Settings';
            } else {
                array_pop($chunks);
                array_shift($chunks);
                array_shift($chunks);
                array_shift($chunks);
                $this->_area = implode(' ', $chunks);
            }
        }
        return $this->_area;
    }

    /**
     * Sync context object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }
        /* @var $modxElement \modSystemSetting */
        $modxElement = self::getModX()->getObject('modSystemSetting', array('key' => $this->getKey(), 'namespace' => $this->getComponent()->getNamespace()->get('name')));
        if ($modxElement) {
            ModSync\Logger::debug('Already exists: ' . get_called_class());
        } else {
            ModSync\Logger::info('Inserting: ' . get_called_class());
            $modxElement = self::getModX()->newObject('modSystemSetting');
            $modxElement->set('key', strtolower($this->getKey()));
            $modxElement->set('namespace', $this->getComponent()->getNamespace()->get('name'));
            $modxElement->set('value', $this->_value);
            $modxElement->set('xtype', $this->_xtype);
            $modxElement->set('area', $this->getArea());
            $this->onInsert();
            $modxElement->save();
        }
    }

    public function onInsert() {
        
    }

    final public function onUpdate() {
        
    }

    /**
     * Is this item syncable?
     *
     * @return boolean
     */
    public function isSyncable() {
        return (bool) $this->_syncable;
    }

}