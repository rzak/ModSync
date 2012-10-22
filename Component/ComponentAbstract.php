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

    use ModSync\Element\Category\HasCategoryTrait;

    const COMPONENT_FILE = 'Component.php';
    const COMPONENT_CLASS = '%s_Component';

    private $_name;
    private $_ns;
    protected $_syncable = true;
    protected $_elements = array(
        'Category' => 'Element/Category',
        'Context' => 'Context',
        'Chunk' => 'Element/Chunk',
//        'Variable' => 'Element/Template/Variable',
        'Template' => 'Element/Template',
        'Snippet' => 'Element/Snippet',
        'Plugin' => 'Element/Plugin',
        'SystemSetting' => 'System/Setting'
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
        $this->_createNamespace();
        $dir = self::getCoreComponentsDir() . DIRECTORY_SEPARATOR . $this->getName();
        foreach ($this->_elements as $element) {
            $this->_syncFolder($dir . DIRECTORY_SEPARATOR . $element);
        }
//        $this->_buildSchema();
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

    protected function _buildSchema() {
        ModSync\Logger::info('disable ' . __METHOD__ . ' for now');
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
        $schema = MODX_CORE_PATH . 'components/' . $name . '/Model/schema/mysql.schema.xml';
        $target = MODX_CORE_PATH . 'components/' . $name . '/Model/';
        $generator->parseSchema($schema, $target);
    }

}