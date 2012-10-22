<?php

namespace ModSync\Element\Category;

use ModSync;

trait HasCategoryTrait {

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

}