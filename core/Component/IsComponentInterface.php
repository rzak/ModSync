<?php

namespace ModSync\Component;

use ModSync;

interface IsComponentInterface extends ModSync\Element\Category\HasCategoryInterface {

    /**
     * Sync component
     */
    public function sync();

    /**
     * Is this item syncable?
     *
     * @return boolean
     */
    public function isSyncable();

    /**
     * Return namespace
     * @return \modNamespace 
     */
    public function getNamespace();
}