<?php

namespace ModSync\Element;

use ReflectionClass;
use SplFileInfo;
use ModSync;

abstract class ElementAbstract extends ModSync\Base implements ModSync\Element\IsElementInterface {
    ###
    # START OF HAS CATEGORY TRAIT
    ###
    /**
     * I disabled the use of traits because I need this module to be php 5.3 complient
     */
    //use ModSync\Element\Category\HasCategoryTrait;

    protected $_category;

    /**
     * Checks if element belongs to category
     *
     * @return boolen
     */
    final public function hasCategory() {
        $this->_category = ModSync\Element\Category\CategoryAbstract::toObject($this->_category);
        if ($this->_category) {
            return true;
        }
        return false;
    }

    /**
     * Returns Category
     *
     * @return ModSync\Element\Category\IsCategoryInterface
     */
    final public function getCategory() {
        if (!$this->hasCategory()) {
            throw new Exception('Element does not belong to category');
        }
        return $this->_category;
    }

    /**
     * Sets category
     *
     * @param mixed
     */
    final public function setCategory($category) {
        $this->_category = ModSync\Element\Category\CategoryAbstract::toObject($category);
    }

    ###
    # END OF HAS CATEGORY TRAIT
    ###

    protected $_syncable = true;
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_locked = 1;
    protected $_properties = array();
    private $_modifiedTime;

    /**
     * Returns element's name
     *
     * @return string
     */
    public function getName() {
        if (null === $this->_name) {
            $chunks = explode('_', str_replace('\\', '_', get_class($this)), 4);
            $this->_name = $chunks[0] . '_' . $chunks[3];
        }
        return $this->_name;
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
    final protected function setProperty($name, $value, $desc = '', $type = 'textfield', $options = array(), $lexicon = null) {
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
     * @return \modElement|false
     */
    final protected function _sync($name, $array) {
        if (!$this->isSyncable()) {
            return false;
        }
        /* @var $modElement \modElement */
        $modElement = self::getModX()->getObject($name, $array);
        if (!$modElement) {
            $modElement = self::getModX()->newObject($name, $array);
            $this->onInsert();
            ModSync\Logger::info('Inserting: ' . get_called_class());
        } else {
            if (!$this->_isSyncAllowed($modElement)) {
                ModSync\Logger::debug('Syncing disabled by user: ' . get_called_class());
                return false;
            }
            if (!$this->_isSyncNeeded($modElement)) {
                ModSync\Logger::debug('No changes to: ' . get_called_class());
                return false;
            }
            $this->onUpdate();
            ModSync\Logger::info('Updating: ' . get_called_class());
        }

        if (null === $modElement) {
            throw new Exception('Trying to sync with null element');
        }
        if ($this instanceof \ModSync\HasContentInterface) {
            $modElement->setContent($this->getContent());
        }
        if ($this->hasCategory()) {
            $modElement->set('category', $this->getCategory()->getId());
        }
        $modElement->set('description', $this->getDescription());
        $modElement->set('locked', intval($this->_locked));
        $modElement->setProperties($this->_properties, true);
        if ($this->onBeforeSave($modElement) !== false) {
            $modElement->save();
        }
        return $modElement;
    }

    /**
     * Hook before save, returning false will abandon save
     * 
     * @param \modElement $modElement
     * @return boolean
     */
    public function onBeforeSave(\modElement &$modElement) {
        return true;
    }

    /**
     * Executes on insert
     */
    public function onInsert() {
        $this->setProperty('modsync_syncable', true, 'Determines if this element has been synced using ModSync plugin', 'combo-boolean');
        $this->setProperty('modsync_last_synced', date('Y-m-d H:i:s', $this->_getModifiedTime()), 'This element was last synced on this date');
        if ($this instanceof \ModSync\HasContentInterface) {
            $this->setProperty('modsync_default_content', $this->getContent(), 'This is the default element content.  It can be used to revert any changes manual changes to this element.', 'textarea');
        }
    }

    /**
     * Executes on update
     */
    public function onUpdate() {
        $this->setProperty('modsync_last_synced', date('Y-m-d H:i:s', $this->_getModifiedTime()), 'This element was last synced on this date');
        if ($this instanceof \ModSync\HasContentInterface) {
            $this->setProperty('modsync_default_content', $this->getContent(), 'This is the default element content.  It can be used to revert any changes manual changes to this element.', 'textarea');
        }
    }

    /**
     * Is Syncable Allowed
     * 
     * @param \modElement $modxElement
     * @return boolean
     */
    private function _isSyncAllowed(\modElement $modxElement) {
        $props = $modxElement->getProperties();
        return (bool) @$props['modsync_syncable'];
    }

    /**
     * Is Sync Needed
     * 
     * @param \modElement $modxElement
     * @return boolean
     */
    private function _isSyncNeeded(\modElement $modxElement) {
        $props = $modxElement->getProperties();
        return strtotime((string) @$props['modsync_last_synced']) < $this->_getModifiedTime();
    }

    /**
     * Get the last modified timestamp
     * 
     * @return int
     */
    private function _getModifiedTime() {
        if (null === $this->_modifiedTime) {
            $class = new ReflectionClass(get_class($this));
            $file = new SplFileInfo($class->getFileName());
            $this->_modifiedTime = $file->getMTime();
        }
        return $this->_modifiedTime;
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
