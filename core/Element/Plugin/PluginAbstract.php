<?php

namespace ModSync\Element\Plugin;

use StdClass;
use ModSync;

/**
 * TODO: There is a bug with modx not working properly with some of these events enabled.
 * I haven't had the time to properly investigate it yet.
 * 
 * It has to do with the return value
 */
abstract class PluginAbstract extends ModSync\Element\ElementAbstract implements ModSync\Element\Plugin\IsPluginInterface {
    ###
    # START OF HAS PROFILING TRAIT
    ###
    /**
     * I disabled the use of traits because I need this module to be php 5.3 complient
     */

    //use ModSync\Element\HasProfilingTrait;
    private $_profiling;

    /**
     * Begin profiling by recording start time
     */
    final public function beginProfiling() {
        if (null === $this->_profiling) {
            $this->_profiling = new StdClass;
            $this->_profiling->beginTime = microtime();
            $this->_profiling->enabled = (boolean) self::getModX()->getOption('modsync__profiling_enabled', null, true);
            $this->_profiling->threshold = (int) self::getModX()->getOption('modsync__profiling_threshold', null, 1);
        }
    }

    /**
     * Report execution time
     */
    final public function reportProfiling() {
        if (null !== $this->_profiling && $this->_profiling->enabled) {
            $this->_profiling->endTime = microtime();

            $begin = explode(' ', $this->_profiling->beginTime);
            $end = explode(' ', $this->_profiling->endTime);
            $this->_profiling->duration = ($end[1] + $end[0]) - ($begin[1] + $begin[0]);

            ModSync\Logger::debug(sprintf(get_called_class() . ': %s (%1.3f seconds)', $this->getName(), $this->_profiling->duration));
            if ($this->_profiling->duration > $this->_profiling->threshold) {
                ModSync\Logger::warn(sprintf(get_called_class() . ': inefficient code - %s (%1.3f seconds)', $this->getName(), $this->_profiling->duration));
            }
        }
    }

    ###
    # END OF HAS PROFILING TRAIT
    ###

    const EVENT_METHOD_PREFIX = 'event';

    public $args;
    protected $_default_event_settings = array('priority' => 0, 'propertyset' => 0);
    protected $_event_settings = array();
    static protected $_available_events;
    private $_events;

    /**
     * Enforces for only event methods to be called dynamically
     *
     * @param string $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments) {
        if (substr($name, 0, strlen(self::EVENT_METHOD_PREFIX)) != self::EVENT_METHOD_PREFIX) {
            $name = self::EVENT_METHOD_PREFIX . $name;
        }
        if (is_callable(array($this, $name))) {
            return $this->$name($arguments);
        }
    }

    /**
     * This is where the magic happens... modx snippet should always call this
     * method to execute the snippet.
     *
     * @return mixed
     */
    final static public function process($args) {
        $class = get_called_class();
        $o = new $class;
        $o->args = (array) $args;
        return $o->run();
    }

    /**
     * Disable plugin
     * 
     * @param string $name 
     */
    final static public function disable($name) {
        ModSync\Logger::error('Missing plugin source: ' . $name);
        /* @var $plugin \modPlugin */
        $plugin = self::getModX()->getObject('modPlugin', array('name' => $name));
        $plugin->set('disabled', true);
        $plugin->save();
        self::getModX()->getCacheManager()->refresh();
    }

    /**
     * This method will be called by modx event trigger.
     * This is where we call the current event handler.
     * 
     * @return mixed
     */
    final public function run() {
        $this->beginProfiling();

        $event_name = $this->getEvent()->name;
        ModSync\Logger::info(get_called_class() . ' - ' . $event_name);
        $return = $this->$event_name($this->getEvent()->params);

        $this->reportProfiling();
        return $return;
    }

    /**
     * Returns the current Event
     * 
     * @return modSystemEvent
     */
    final public function getEvent() {
        return self::getModX()->event;
    }

    /**
     * Returns the snippet content
     * 
     * @return string
     */
    final public function getContent() {
        $content = '
if (file_exists(\ModSync\Base::getCoreComponentsDir() . DIRECTORY_SEPARATOR . "' . str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)) . '.php")) {
    return \\' . get_class($this) . '::process(array("properties" => $scriptProperties));
} else {
    return \\ModSync\\Element\\Plugin\\PluginAbstract::disable("' . $this->getName() . '");
}';
        return $content;
    }

    /**
     * Sync plugin object
     */
    final public function sync() {
        /* @var $plugin \modPlugin */
        $plugin = $this->_sync('modPlugin', array('name' => $this->getName()));

        if ($plugin) {
            // remove existing plugin events
            $pluginEvents = self::getModX()->getCollection('modPluginEvent', array('pluginid' => $plugin->get('id')));
            foreach ($pluginEvents as $pluginEvent) {
                $pluginEvent->remove();
            }

            // add new plugin events
            foreach ($this->_getEvents() as $eventName => $setting) {
                $pluginEvent = self::getModX()->newObject('modPluginEvent');
                $pluginEvent->set('pluginid', $plugin->get('id'));
                $pluginEvent->set('event', $eventName);
                $pluginEvent->set('priority', intval($setting['priority']));
                $pluginEvent->set('priority', intval($setting['propertyset']));
                $pluginEvent->save();
            }
        }
    }

    /**
     * Returns with defined Events
     * 
     * @return array
     */
    final private function _getEvents() {
        if (null === $this->_events) {
            $this->_events = array();
            $methods = $this->_getMethods();
            foreach ($methods as $method) {
                if (strpos(substr($method, 0, strlen(self::EVENT_METHOD_PREFIX)), self::EVENT_METHOD_PREFIX) !== false) {
                    $eventName = substr($method, strlen(self::EVENT_METHOD_PREFIX));
                    if (!in_array($eventName, self::_getAvailableEvents())) {
                        continue;
                    }
                    $event = $this->_default_event_settings;
                    if (isset($this->_event_settings[$eventName])) {
                        $event = array_merge($event, $this->_event_settings[$eventName]);
                    }
                    $this->_events[$eventName] = $event;
                }
            }
        }
        return $this->_events;
    }

    /**
     * Returns class methods
     * 
     * @return array
     */
    final private function _getMethods() {
        return get_class_methods($this);
    }

    final static protected function _getAvailableEvents() {
        if (null === self::$_available_events) {
            self::$_available_events = array();
            $c = self::getModX()->getCollection('modEvent');
            foreach ($c as $e) {
                self::$_available_events[] = $e->get('name');
            }
        }
        return self::$_available_events;
    }

}