<?php

class ModSync_Context_Setting_Abstract extends ModSync_Base implements ModSync_IsSyncable {
    const TYPE_TEXTFIELD = 'textfield';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_YESNO = 'combo-boolean';

    protected $_syncable = true;
    protected $_context_key;
    protected $_key;
    protected $_value;
    protected $_xtype;
    protected $_namespace;
    protected $_area;

    public function __construct($context_key, $key, $value, $xtype, $namespace, $area) {
        parent::__construct();
        $this->set('context_key', $context_key);
        $this->set('key', $key);
        $this->set('value', $value);
        $this->set('xtype', $xtype);
        $this->set('namespace', $namespace);
        $this->set('area', $area);
        $this->set('context_key', $context_key);
    }

    public function set($key, $val) {
        $f = '_' . $key;
        $this->$f = $val;
    }

    /**
     * Sync context object
     */
    final public function sync() {
        /* @var $modxElement modContextSetting */
        if (!$modxElement = self::getModX()->getObject('modContextSetting', array('key' => $this->_key, 'context_key' => $this->_context_key))) {
            $modxElement = self::getModX()->newObject('modContextSetting');
            $modxElement->set('key', $this->_key);
            $modxElement->set('context_key', $this->_context_key);
            $this->onInsert();
        } else {
            $this->onUpdate();
        }
        $modxElement->set('value', $this->_value);
        $modxElement->set('xtype', $this->_xtype);
        $modxElement->set('namespace', $this->_namespace);
        $modxElement->set('area', $this->_area);
        $modxElement->save();
    }

    public function onInsert() {
        
    }

    public function onUpdate() {
        
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