<?php

namespace ModSync\Element\Category;

use ModSync;

interface HasCategoryInterface {

    /**
     * Returns category
     *
     * @return ModSync\Element\Category\IsCategoryInterface
     */
    public function getCategory();

    /**
     * Checks if element belongs to category
     * 
     * @return boolen
     */
    public function hasCategory();

    /**
     * Sets category
     *
     * @param mixed
     */
    public function setCategory($category);
}