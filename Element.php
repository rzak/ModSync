<?php

abstract class ModSync_Element extends ModSync_Base implements ModSync_IsSyncable, ModSync_HasCategory, ModSync_HasContent {

    protected $_syncable = true;
    protected $_id;
    protected $_name;
    protected $_category;
    protected $_description;
    protected $_locked = 1;
    private $_properties = array();

    /**
     * Returns element's name
     *
     * @return string
     */
    final public function getName() {
        if (null === $this->_name) {
            $chunks = explode('_', get_class($this), 3);
            $this->_name = $chunks[0] . '_' . $chunks[2];
        }
        return $this->_name;
    }

    /**
     * Checks if element belongs to category
     *
     * @return boolen
     */
    final public function hasCategory() {
        $this->_category = ModSync_Category_Abstract::toObject($this->_category);
        if ($this->_category) {
            return true;
        }
        return false;
    }

    /**
     * Returns Category
     *
     * @return ModSync_Category_Abstract
     */
    final public function getCategory() {
        if (!$this->hasCategory()) {
            throw new ModSync_Exception('Element does not belong to category');
        }
        return $this->_category;
    }

    /**
     * Sets category
     *
     * @param mixed
     */
    final public function setCategory($category) {
        $this->_category = ModSync_Category_Abstract::toObject($category);
    }

    /**
     * Returns the description field
     *
     * @return string
     */
    final public function getDescription() {
        if (null === $this->_description) {
            $this->_description = 'Auto generated description for ' . $this->getName();
        }
        return $this->_description;
    }

    /**
     * Sets Property for element
     *
     * @param string $name
     * @param string $value
     * @param string $desc
     * @param string $type
     * @param array $options
     * @param string $lexicon
     */
    final protected function setProperty($name, $value, $desc = '', $type='textfield', $options = array(), $lexicon = null) {
        $this->_properties[$name] = array(
            'name' => $name,
            'desc' => $desc,
            'type' => $type,
            'options' => $options,
            'value' => $value,
            'lexicon' => $lexicon
        );
    }

    /**
     * Syncs an element with modx
     *
     * @param string $name Element type
     * @param array $array Primary key
     * @return modElement
     */
    final protected function _sync($name, $array) {
        /* @var $modxElement modElement */
        if (!$modxElement = self::getModX()->getObject($name, $array)) {
            $modxElement = self::getModX()->newObject($name, $array);
            $this->onInsert();
        } else {
            $props = $modxElement->getProperties();
            $syncable = (bool) @$props['modsync_syncable'];
            if (!$syncable) {
                self::log('Syncing Disabled: ' . $this->getName(), Zend_Log::NOTICE);
                return $modxElement;
            }
            $this->onUpdate();
        }

        if (null === $modxElement) {
            throw new ModSync_Exception('Trying to sync with null element');
        }
        $modxElement->setContent($this->getContent());
        if ($this->hasCategory()) {
            $modxElement->set('category', $this->getCategory()->getId());
        }
        $modxElement->set('description', $this->getDescription());
        $modxElement->set('locked', intval($this->_locked));
        $modxElement->setProperties($this->_properties, true);
        $modxElement->save();
        return $modxElement;
    }

    /**
     * Executes on insert
     */
    public function onInsert() {
        $this->setProperty('modsync_syncable', '1', 'Determines if this element has been synced using ModSync plugin', 'combo-boolean');
        $this->setProperty('modsync_last_synced', date('Y-m-d H:i:s'), 'This element was last synced on this date');
        $this->setProperty('modsync_default_content', $this->getContent(), 'This is the default element content.  It can be used to revert any changes manual changes to this element.', 'textarea');
    }

    /**
     * Executes on update
     */
    public function onUpdate() {
        $this->setProperty('modsync_last_synced', date('Y-m-d H:i:s'), 'This element was last synced on this date');
        $this->setProperty('modsync_default_content', $this->getContent(), 'This is the default element content.  It can be used to revert any changes manual changes to this element.', 'textarea');
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
