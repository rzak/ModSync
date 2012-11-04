<?php

namespace ModSync\Element\Category;

use ModSync;

interface IsCategoryInterface extends ModSync\IsSyncableInterface {

    /**
     * Returns element's name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Parent
     *
     * @return ModSync\Element\Category\IsCategoryInterface
     */
    public function getParent();

    /**
     * Returns category id
     *
     * @return int
     */
    public function getId();

    /**
     * Returns a ModSync\Element\Category\IsCategoryInterface object or null
     * 
     * @todo look into this logic to make sure it still applies with namespaces
     * @param ModSync\Element\Category\IsCategoryInterface|string $category
     * @return ModSync\Element\Category\IsCategoryInterface|null
     */
    static public function toObject($category);
}