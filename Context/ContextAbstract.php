<?php

namespace ModSync\Context;

use ModSync;

abstract class ContextAbstract extends ModSync\Base implements ModSync\IsSyncableInterface {

    protected $_syncable = true;
    protected $_key;
    protected $_description;
    protected $_rank = 0;

    /**
     * Returns context key
     *
     * @return string
     */
    final public function getKey() {
        if (null === $this->_key) {
            $chunks = explode('_', str_replace('\\', '_', get_class($this)), 3);
            $this->_key = $chunks[0] . '_' . $chunks[2];
        }
        return strtolower($this->_key);
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
            return $this;
        }


        /* @var $context \modContext */
        $context = self::getModX()->getObject('modContext', array('key' => $this->getKey()));
        if ($context) {
            ModSync\Logger::debug('Already exists: ' . get_called_class());
            $this->onUpdate();
        } else {
            ModSync\Logger::info('Inserting: ' . get_called_class());
            $context = self::getModX()->newObject('modContext');
            $context->set('key', $this->getKey());
            $context->set('description', $this->getDescription());
            $context->set('rank', (int) $this->_rank);
            $this->onInsert();
            $context->save();
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
    final public function isSyncable() {
        return (bool) $this->_syncable;
    }

}