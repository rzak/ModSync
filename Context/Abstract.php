<?php

abstract class ModSync_Context_Abstract extends ModSync_Base implements ModSync_IsSyncable {

    protected $_syncable = true; // sync disabled
    protected $_key;
    protected $_description;
    protected $_settings;

    final public function addSetting($key, $value = '', $xtype = ModSync_Context_Setting_Abstract::TYPE_TEXTFIELD, $namespace = 'core', $area = 'custom') {
        if (!is_array($this->_settings)) {
            $this->_settings = array();
        }
        $this->_settings[] = new ModSync_Context_Setting_Abstract($this->getKey(), $key, $value, $xtype, $namespace, $area);
    }

    final private function getSettings() {
        if (null === $this->_settings) {
            $this->_settings = array();
        }
        return $this->_settings;
    }

    /**
     * Returns context key
     *
     * @return string
     */
    final public function getKey() {
        if (null === $this->_key) {
            $chunks = explode('_', get_class($this), 3);
            $this->_key = $chunks[0] . '_' . $chunks[2];
        }
        return $this->_key;
    }

    /**
     * Returns the description field
     *
     * @return string
     */
    final public function getDescription() {
        if (null === $this->_description) {
            $this->_description = 'Auto generated description for ' . $this->getKey();
        }
        return $this->_description;
    }

    /**
     * Sync context object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        self::log('Syncing Context: ' . $this->getKey(), Zend_Log::INFO);


        /* @var $context modContext */
        if (!$context = self::getModX()->getObject('modContext', array('key' => $this->getKey()))) {
            $context = self::getModX()->newObject('modContext');
            $context->set('key', $this->getKey());
            $this->onInsert();
        } else {
            $this->onUpdate();
        }
        $context->set('description', $this->getDescription());
        $context->save();
        foreach ($this->getSettings() as $setting) {
            $setting->sync();
        }
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