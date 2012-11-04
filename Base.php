<?php

namespace ModSync;

use ModSync;

class Base {

    private $_component;

    /**
     * Construction
     * 
     * @throws \ModSync\Exception 
     */
    public function __construct() {
        if (!(self::getModX() instanceof \modX)) {
            throw new \ModSync\Exception('modx object not found');
        }
    }

    /**
     * Returns global modx object
     *
     * @global \modX $modx
     * @return \modX
     */
    final public static function getModX() {
        global $modx;
        return $modx;
    }

    /**
     * Returns the web root directory
     * 
     * @return string
     * @throws \ModSync\Exception
     */
    final public static function getWebRootDir() {
        if (!defined('__WEB_ROOT_DIR__')) {
            throw new \ModSync\Exception('`__WEB_ROOT_DIR__` should be defined in auto prepend file');
        }
        return __WEB_ROOT_DIR__;
    }

    /**
     * Returns the modx core directory
     * 
     * @return string
     */
    final public static function getCoreDir() {
        return self::getWebRootDir() . DIRECTORY_SEPARATOR . 'core';
    }

    /**
     * Returns the assets directory
     * 
     * @return string
     */
    final public static function getAssetsDir() {
        return self::getWebRootDir() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets';
    }

    /**
     * Returns asset components directory
     * 
     * @return string
     */
    final public static function getAssetsComponentsDir() {
        return self::getAssetsDir() . DIRECTORY_SEPARATOR . 'components';
    }

    /**
     * Returns core components directory
     * 
     * @return string
     */
    final public static function getCoreComponentsDir() {
        return self::getCoreDir() . DIRECTORY_SEPARATOR . 'components';
    }

    /**
     * Returns log directory
     * 
     * @return string
     */
    final public static function getLogDir() {
        return dirname(self::getWebRootDir()) . DIRECTORY_SEPARATOR . 'log';
    }

    /**
     * Clears modx cache files
     */
    final public static function clearCache() {
        self::getModX()->getCacheManager()->refresh();
    }

    /**
     * Add custom package
     * 
     * @param string $component 
     * @return \modX
     */
    final public static function addPackage($component) {
        self::getModX()->addPackage(strtolower($component), self::getCoreComponentsDir() . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR, '');
        return self::getModX();
    }

    /**
     * Returns component
     * 
     * @return ModSync\Component\ComponentAbstract
     */
    final public function getComponent() {
        if (null === $this->_component) {
            $chunks = explode('\\', get_called_class(), 2);
            $name = '\\' . $chunks[0] . '\\Component\\Component';
            $this->_component = new $name;
        }
        return $this->_component;
    }

}