<?php

namespace ModSync\Element\Template\Variable;

use ModSync;

interface IsVariableInterface extends ModSync\IsSyncableInterface, ModSync\Element\Category\HasCategoryInterface {

    /**
     * Returns caption
     *
     * @return string
     */
    public function getCaption();

    /**
     * Returns tv's type
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the description field
     *
     * @return string
     */
    public function getDescription();
}