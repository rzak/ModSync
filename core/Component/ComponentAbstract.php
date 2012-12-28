<?php

namespace ModSync\Component;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use ReflectionClass;
use ReflectionException;
use LogicException;
use ModSync;

abstract class ComponentAbstract extends ModSync\Base implements ModSync\Component\IsComponentInterface {
    ###
    # START OF HAS CATEGORY TRAIT
    ###
    /**
     * I disabled the use of traits because I need this module to be php 5.3 complient
     */

    //use ModSync\Element\Category\HasCategoryTrait;

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

    ###
    # END OF HAS CATEGORY TRAIT
    ###

    const COMPONENT_FILE = 'Component.php';
    const COMPONENT_CLASS = '%s_Component';

    private $_name;
    private $_ns;
    protected $_syncable = true;
    protected $_elements = array(
        'Category' => 'Element/Category',
        'Context' => 'Context',
        'ContextSetting' => 'Context/Setting',
        'SystemSetting' => 'System/Setting',
        'MediaSource' => 'System/MediaSource',
        'Chunk' => 'Element/Chunk',
        'Variable' => 'Element/Template/Variable',
        'Template' => 'Element/Template',
        'Snippet' => 'Element/Snippet',
        'Plugin' => 'Element/Plugin',
    );

    final public function getName() {
        if (null === $this->_name) {
            $chunks = explode('\\', get_class($this), 2);
            $this->_name = $chunks[0];
        }
        return $this->_name;
    }

    /**
     * Sync component
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }
        ModSync\Logger::notice('Syncing: ' . $this->getName());
        $this->beforeSyncHook();
        $this->_createNamespace();
        $dir = self::getCoreComponentsDir() . DIRECTORY_SEPARATOR . $this->getName();
        foreach ($this->_elements as $element) {
            $this->_syncFolder($dir . DIRECTORY_SEPARATOR . $element);
        }
        $this->afterSyncHook();
    }

    /**
     * Add custom package
     */
    final public function addExtensionPackage() {
        self::getModX()->addExtensionPackage($this->getName(), '[[++core_path]]components' . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR);
    }

    /**
     * A hook for before sync processes
     */
    public function beforeSyncHook() {
        
    }

    /**
     * A hook for after sync processes
     */
    public function afterSyncHook() {
        
    }

    /**
     * Is this item syncable?
     *
     * @return boolean
     */
    final public function isSyncable() {
        return (bool) $this->_syncable;
    }

    final private function _createNamespace() {
        if (!$this->getNamespace()) {
            /* @var $ns \modNamespace */
            $this->_ns = self::getModX()->newObject('modNamespace');
            $this->_ns->set('name', strtolower($this->getName()));
            $this->_ns->set('path', '{core_path}components' . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR);
            $this->_ns->save();
        }
    }

    /**
     * Return namespace
     * @return \modNamespace 
     */
    final public function getNamespace() {
        if ($this->_ns === null) {
            $this->_ns = self::getModX()->getObject('modNamespace', strtolower($this->getName()));
        }
        return $this->_ns;
    }

    /**
     * Loops through elements to be synced
     *
     * @param string $path - path
     *
     * @todo Improve error handling of this method.
     */
    private function _syncFolder($path) {
        $dir = new \SplFileInfo($path);
        if (!$dir->isDir()) {
            return;
        }

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        $filePath = '';
        while ($it->valid()) {
            /* @var $file SplFileInfo */
            $file = $it->current();
            if ($filePath != $file->getPathname() && !$it->isDir() && $it->getExtension() == 'php') {
                $filePath = $file->getPathname();
                $name = str_replace(DIRECTORY_SEPARATOR, '\\', substr($file->getPathname(), strlen(self::getCoreComponentsDir() . DIRECTORY_SEPARATOR), ((strlen($file->getExtension()) + 1 ) * -1)));
                try {
                    $class = new ReflectionClass($name);
                    if (!$class->isAbstract() && !$class->isInterface() && !$class->isTrait() && $class->implementsInterface('ModSync\IsSyncableInterface')) {
                        /* @var $o ModSync\IsSyncableInterface */
                        $o = $class->newInstance();
                        if ($o->isSyncable()) {
                            if ($class->implementsInterface('ModSync\Element\Category\HasCategoryInterface')) {
                                if (!$o->hasCategory() && $this->hasCategory()) {
                                    $o->setCategory($this->getCategory());
                                }
                            }
                            $o->sync();
                        }
                    }
                } catch (LogicException $e) {
                    ModSync\Logger::warn($e->getMessage());
                } catch (ReflectionException $e) {
                    ModSync\Logger::warn($e->getMessage());
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
            $it->next();
        }
    }

}