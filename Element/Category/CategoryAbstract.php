<?php

namespace ModSync\Element\Category;

use ModSync;

abstract class CategoryAbstract extends ModSync\Base implements ModSync\Element\Category\IsCategoryInterface {

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
            $chunks = explode('_', str_replace('\\', '_', get_class($this)), 4);
            $this->_name = $chunks[0] . '_' . $chunks[3];
        }
        return $this->_name;
    }

    /**
     * Get Parent
     *
     * @return  ModSync\Element\Category\IsCategoryInterface|null
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
            /* @var $obj \modCategory */
            $obj = self::getModX()->getObject('modCategory', array('category' => $this->getName()));
            if (!$obj) {
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

        $parent = $this->getParent();
        /* @var $category \modCategory */
        $category = self::getModX()->getObject('modCategory', array('category' => $this->getName()));
        if (!$category) {
            ModSync\Logger::info('Inserting: ' . get_called_class());
            $category = self::getModX()->newObject('modCategory');
            $category->set('category', $this->getName());
            if ($parent) {
                $category->set('parent', $parent->getId());
            }
            $this->onInsert();
            $category->save();
        } else {
            if (($parent && $parent->getId() != $category->get('parent')) || (!$parent && intval($category->get('parent')) > 0)) {
                ModSync\Logger::info('Updating: ' . get_called_class());
                if ($parent) {
                    $category->set('parent', intval($parent->getId()));
                } else {
                    $category->set('parent', 0);
                }
                $this->onUpdate();
                $category->save();
            } else {
                ModSync\Logger::debug('No changes to: ' . get_called_class());
            }
        }
        $this->_id = $category->get('id');
    }

    /**
     * On insert hook 
     */
    public function onInsert() {
        
    }

    /**
     * On update hook 
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

    /**
     * Returns a ModSync\Element\Category\IsCategoryInterface object or null
     * 
     * @todo look into this logic to make sure it still applies with namespaces
     * @param  ModSync\Element\Category\IsCategoryInterface|string $category
     * @return  ModSync\Element\Category\IsCategoryInterface|null
     */
    static public function toObject($category) {
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
