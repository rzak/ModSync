<?php

namespace ModSync\Resource;

use ModSync;

abstract class ResourceAbstract extends ModSync\Base implements ModSync\IsSyncableInterface, ModSync\HasContentInterface {

    protected $_syncable = true;
    protected $_id;
    protected $_locked = 1;
    protected $_type = 'document';
    protected $_content_type = 'text/html';
    protected $_pagetitle;
    protected $_template = 0;
    protected $_published = 1;
    protected $_class_key = 'modDocument';
    protected $_context_key = 'web';
    protected $_content_type = 1;

    /**
     * Returns element's name
     *
     * @return string
     */
    final public function getName() {
        return $this->getPageTitle();
    }

    /**
     * Returns page title
     *
     * @return string
     */
    final public function getPageTitle() {
        if (null === $this->_pagetitle) {
            $chunks = explode('_', get_class($this), 3);
            $this->_pagetitle = $chunks[0] . '_' . $chunks[2];
        }
        return $this->_pagetitle;
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
     * Syncs an element with modx
     *
     * @param string $name Element type
     * @param array $array Primary key
     * @return modElement
     */
    final protected function _sync($name, $array) {
        /* @var $modxResource modResource */
        $modxResource = self::getModX()->newObject($name, $array);
        $this->onInsert();

        if (null === $modxResource) {
            throw new Exception('Trying to sync with null element');
        }
        $modxResource->setContent($this->getContent());
        if ($this->hasCategory()) {
            $modxResource->set('category', $this->getCategory()->getId());
        }
        $modxResource->set('description', $this->getDescription());
        $modxResource->set('locked', intval($this->_locked));
        $modxResource->setProperties($this->_properties, true);
        $modxResource->save();
        return $modxResource;
    }

    /**
     * Executes on insert
     */
    public function onInsert() {
        
    }

    /**
     * Executes on update
     */
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
