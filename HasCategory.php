<?php

interface ModSync_HasCategory
{

    /**
     * Returns category
     *
     * @return ModSync_Category_Abstract
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