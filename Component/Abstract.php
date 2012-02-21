<?php

class ModSync_Component_Abstract extends ModSync_Base implements ModSync_HasCategory {

    private $_name;
    protected $_syncable = true;
    protected $_category;
    protected $_elements = array('Context', 'Category', 'Chunk', 'Variable', 'Template', 'Snippet', 'Plugin');

    final public function getName() {
        if (null === $this->_name) {
            $chunks = explode('_', get_class($this), 2);
            $this->_name = $chunks[0];
        }
        return $this->_name;
    }

    /**
     * Checks if element belongs to category
     *
     * @return boolen
     */
    final public function hasCategory() {
        $this->_category = ModSync_Category_Abstract::toObject($this->_category);
        if ($this->_category) {
            return true;
        }
        return false;
    }

    /**
     * Returns Category
     *
     * @return ModSync_Category_Abstract
     */
    final public function getCategory() {
        if (!$this->hasCategory()) {
            throw new ModSync_Exception('Element does not belong to category');
        }
        return $this->_category;
    }

    /**
     * Sets category
     *
     * @param mixed
     */
    final public function setCategory($category) {
        $this->_category = ModSync_Category_Abstract::toObject($category);
    }

    /**
     * Sync component
     */
    public function sync() {
        if ((bool) $this->_syncable === false) {
            return;
        }

        foreach ($this->_elements as $element) {
            $this->_syncElements($element);
        }
        
        $this->_buildSchema();
    }

    /**
     * Loops through elements to be synced
     *
     * @param string $type - element type (etc. Template, Chunk, Snippet, etc)
     * @param string $folder - folder name
     *
     * @todo Improve error handling of this method.
     */
    private function _syncElements($type, $folder = '') {
        $path = rtrim(MODX_CORE_PATH . sprintf('components/%s/%s/%s', $this->getName(), $type, $folder), '/');
        if (!is_dir($path)) {
            return;
        }
        $elements = scandir($path);
        $folders = array();
        foreach ($elements as $element) {
            // anything starting with a '.' should be ignored
            if (substr($element, 0, 1) == '.') {
                continue;
            }

            // check for next level directories
            if (is_dir($path . '/' . $element)) {
                $folders[] = $folder . '/' . $element;
                continue;
            }

            // if not php file, ignore
            if (!self::getModX()->getCacheManager()->endsWith($element, '.php')) {
                continue;
            }
            // ignore abstract classes
            if (self::getModX()->getCacheManager()->endsWith($element, 'Abstract.php')) {
                continue;
            }

            // define some vars and sync elements
            $folder2 = trim(str_replace('/', '_', $folder . '/'), '_');
            if (!empty($folder2)) {
                $folder2.= '_';
            }
            $element = substr($element, 0, -4); //trim ext (.php)
            $class_name = sprintf('%s_%s_%s%s', $this->getName(), $type, $folder2, $element);
            if (!class_exists($class_name)) {
                throw new ModSync_Exception('Class does not exist: ' . $class_name);
            }
            $o = new $class_name();
            if (in_array('ModSync_IsSyncable', class_implements($o))) {
                if ($o->isSyncable()) {
                    if (in_array('ModSync_HasCategory', class_implements($o))) {
                        if (!$o->hasCategory() && $this->hasCategory()) {
                            $o->setCategory($this->getCategory());
                        }
                    }
                    $o->sync();
                }
            }
        }
        foreach ($folders as $folder) {
            $this->_syncElements($type, trim($folder, '/'));
        }
    }

    protected function _buildSchema() {
        $name = $this->getName();
        $schema = MODX_CORE_PATH . 'components/' . $name . '/Model/schema/mysql.schema.xml';
        $target = MODX_CORE_PATH . 'components/' . $name . '/Model/';
        if (!is_file($schema)) {
            return;
        }
        
        
        $manager = self::getModX()->getManager();
        $generator = $manager->getGenerator();
        $generator->classTemplate = <<<EOD
<?php
/**
 * [+phpdoc-package+]
 */
class [+class+] extends [+extends+] {}
?>
EOD;
        $generator->platformTemplate = <<<EOD
<?php
/**
 * [+phpdoc-package+]
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\\\', '/') . '/[+class-lowercase+].class.php');
class [+class+]_[+platform+] extends [+class+] {}
?>
EOD;
        $generator->mapHeader = <<<EOD
<?php
/**
 * [+phpdoc-package+]
 */
EOD;
        $name = $this->getName();
        $schema = MODX_CORE_PATH . 'components/' . $name . '/Model/schema/mysql.schema.xml';
        $target = MODX_CORE_PATH . 'components/' . $name . '/Model/';
        $generator->parseSchema($schema, $target);
    }

}