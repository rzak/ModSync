<?php

namespace ModSync\Element;

use ModSync;

interface IsElementInterface extends ModSync\IsSyncableInterface, ModSync\Element\Category\HasCategoryInterface, ModSync\HasContentInterface {

    /**
     * Returns element's name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the description field
     *
     * @return string
     */
    public function getDescription();
}