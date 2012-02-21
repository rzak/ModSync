<?php

class ModSync_Base {

    /**
     * Logger object
     *
     * @var Zend_Log
     */
    private static $_logger;
    private static $_logger_priority;

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
     * @global modX $modx
     * @return modX
     */
    final public static function getModX() {
        global $modx;
        return $modx;
    }

    /**
     * Returns the modx element id
     * 
     * @param ModSync_Base $obj
     * @return int
     */
    final public static function getModXId(ModSync_Base $obj) {
        if (!in_array('ModSync_HasId', class_implements($obj))) {
            throw new ModSync_Exception('object does not have id');
        }
        return $obj->_getModXId();
    }

    /**
     * Simple logging
     * 
     * @param mixed $msg 
     */
    final public static function log($message, $priority = 3) {
        if (null === self::$_logger_priority) {
            self::$_logger_priority = intval($_SERVER['LOG_PRIORITY']);
        }
        if ($priority <= self::$_logger_priority) {
            if (null === self::$_logger) {
                self::$_logger = new Zend_Log(new Zend_Log_Writer_Stream(dirname(__WEB_ROOT_DIR__) . '/log/' . date('Ymd') . '-' . $_SERVER['LOG_FILE']));
            }
            if (is_array($message)) {
                $message = print_r($message, true);
            }
            if ($priority < Zend_Log::DEBUG) {
                $message .= ' (REQUEST_URI: ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ')';
            }
            if ($priority < Zend_Log::INFO) {
                $message .= ' (REFERER: ' . $_SERVER['HTTP_REFERER'] . ')';
            }
            self::$_logger->log($message, $priority);
        }
    }

    final public static function clearCache() {
        self::getModX()->getCacheManager()->clean();
    }

    /**
     * Add custom package
     * 
     * @param string $component 
     * @return modX
     */
    final public static function addPackage($component) {
        self::getModX()->addPackage(strtolower($component), MODX_CORE_PATH . 'components/' . $component . '/Model/', '');
        return self::getModX();
    }

}