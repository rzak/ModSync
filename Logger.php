<?php

namespace ModSync;

use Zend;

class Logger extends Base {

    const ALERT = Zend\Log\Logger::ALERT;
    const CRIT = Zend\Log\Logger::CRIT;
    const ERR = Zend\Log\Logger::ERR;
    const WARN = Zend\Log\Logger::WARN;
    const NOTICE = Zend\Log\Logger::NOTICE;
    const INFO = Zend\Log\Logger::INFO;
    const DEBUG = Zend\Log\Logger::DEBUG;

    private static $_logger;
    private static $_priority;
    private static $_enabled;

    /**
     * Alert
     * 
     * @param mixed $message
     */
    final public static function alert($message) {
        self::log($message, self::ALERT);
    }

    /**
     * Critical
     * 
     * @param mixed $message
     */
    final public static function critical($message) {
        self::log($message, self::CRIT);
    }

    /**
     * Error
     * 
     * @param mixed $message
     */
    final public static function error($message) {
        self::log($message, self::ERR);
    }

    /**
     * Warning
     * 
     * @param mixed $message
     */
    final public static function warn($message) {
        self::log($message, self::WARN);
    }

    /**
     * Notice
     * 
     * @param mixed $message
     */
    final public static function notice($message) {
        self::log($message, self::NOTICE);
    }

    /**
     * Info
     * 
     * @param mixed $message
     */
    final public static function info($message) {
        self::log($message, self::INFO);
    }

    /**
     * Debug
     * 
     * @param mixed $message
     */
    final public static function debug($message) {
        self::log($message, self::DEBUG);
    }

    /**
     * Simple logging
     * 
     * @param mixed $msg 
     * @return void
     */
    final private static function log($message, $priority = self::INFO) {
        if (null === self::$_enabled) {
            self::$_enabled = self::getModX()->getOption('modsync__logger_enabled', null, true);
        }
        if (self::$_enabled) {
            if (null === self::$_priority) {
                self::$_priority = intval(self::getModX()->getOption('modsync__logger_priority', null, self::DEBUG));
            }
            if ($priority <= self::$_priority) {
                if (null === self::$_logger) {
                    self::$_logger = new Zend\Log\Logger;
                    self::$_logger->addWriter(new Zend\Log\Writer\Stream(self::getLogDir() . DIRECTORY_SEPARATOR . self::getModX()->getOption('modsync__logger_file', null, 'modsync.log')));
                }
                if (is_array($message)) {
                    $message = print_r($message, true);
                }
                self::$_logger->log($priority, $message);
            }
        }
    }

}