<?php

abstract class ModSync_Category_Abstract extends ModSync_Base implements ModSync_IsSyncable {

    protected $_syncable = true;
    protected $_id;
    protected $_name;
    protected $_parent;

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
     * Get Parent
     *
     * @return ModSync_Category_Abstract
     */
    final public function getParent() {
        return self::toObject($this->_parent);
    }

    /**
     * Returns category id
     *
     * @return int
     */
    final public function getId() {
        if (null === $this->_id) {
            /* @var $obj modCategory */
            if (!$obj = self::getModX()->getObject('modCategory', array('category' => $this->getName()))) {
                $this->sync();
                return $this->getId();
            }
            $this->_id = $obj->get('id');
        }
        return $this->_id;
    }

    /**
     * Syncs category object with modx
     *
     * @param mixed
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        self::log('Syncing Category: ' . $this->getName(), Zend_Log::INFO);

        /* @var $category modCategory */
        if (!$category = self::getModX()->getObject('modCategory', array('category' => $this->getName()))) {
            $array = array('category' => $this->getName());
            if ($this->getParent()) {
                $array['parent'] = $this->getParent()->getId();
            }
            $category = self::getModX()->newObject('modCategory', $array);
            $this->onInsert();
            $category->save();
        } else {
            if ($this->getParent()) {
                $category->set('parent', $this->getParent()->getId());
                $category->save();
            }
            $this->onUpdate();
        }
        $this->_id = $category->get('id');
        unset($category);
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

    /**
     * Returns a ModSync_Category_Abstract object or null
     * 
     * @param mixed $category
     * @return ModSync_Category_Abstract|null
     */
    static public function toObject($category = null) {
        if (is_object($category) && is_a($category, __CLASS__)) {
            return $category;
        }
        if (is_string($category) && class_exists($category)) {
            $o = new $category();
            if (is_a($o, __CLASS__)) {
                return $o;
            }
        }
        return null;
    }

}
