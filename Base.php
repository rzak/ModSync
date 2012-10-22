<?php

namespace ModSync;

use ModSync;

class Base {

    private $_component;

    /**
     * Construction
     * @throws Exception 
     */
    public function __construct() {
        try {
            if (!is_a(self::getModX(), 'modX')) {
                throw new Exception('modx object not found');
            }
        } catch (Exception $e) {

            echo $e->getMessage();
            die();
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

    final public static function getWebRootDir() {
        return __WEB_ROOT_DIR__;
    }

    final public static function getCoreDir() {
        return self::getWebRootDir() . DIRECTORY_SEPARATOR . 'core';
    }

    final public static function getAssetsDir() {
        return self::getWebRootDir() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets';
    }

    final public static function getAssetsComponentsDir() {
        return self::getAssetsDir() . DIRECTORY_SEPARATOR . 'components';
    }

    final public static function getCoreComponentsDir() {
        return self::getCoreDir() . DIRECTORY_SEPARATOR . 'components';
    }

    final public static function getLogDir() {
        return dirname(self::getWebRootDir()) . DIRECTORY_SEPARATOR . 'log';
    }

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
     * Get component
     * @return ModSync\Component\ComponentAbstract
     */
    final public function getComponent() {
        if (null === $this->_component) {
            $chunks = explode('\\', get_class($this), 2);
            $name = '\\' . $chunks[0] . '\\Component\\Component';
            $this->_component = new $name;
        }
        return $this->_component;
    }

}