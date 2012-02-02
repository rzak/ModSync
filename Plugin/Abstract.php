<?php

/**
 * TODO: There is a bug with modx not working properly with some of these events enabled.
 * I haven't had the time to properly investigate it yet.
 */
abstract class ModSync_Plugin_Abstract extends ModSync_Element {
    const EVENT_METHOD_PREFIX = 'event';
    protected $_scriptProperties;
    protected $_default_event_settings = array('priority' => 0, 'propertyset' => 0);
    protected $_event_settings = array();
    protected $_available_events = array(
        'OnBeforeCacheUpdate',
        'OnBeforeChunkFormDelete',
        'OnBeforeChunkFormSave',
        'OnBeforeDocFormDelete',
        'OnBeforeDocFormSave',
        'OnBeforeEmptyTrash',
        'OnBeforeManagerLogin', // Something wrong with this event
        'OnBeforeManagerLogout',
        'OnBeforeManagerPageInit',
        'OnBeforePluginFormDelete',
        'OnBeforePluginFormSave',
        'OnBeforeSaveWebPageCache',
        'OnBeforeSnipFormDelete',
        'OnBeforeSnipFormSave',
        'OnBeforeTempFormDelete',
        'OnBeforeTempFormSave',
        'OnBeforeTVFormDelete',
        'OnBeforeTVFormSave',
        'OnBeforeUserActivate',
        'OnBeforeUserFormDelete',
        'OnBeforeUserFormSave',
        'OnBeforeWebLogin',
        'OnBeforeWebLogout',
        'OnCacheUpdate',
        'OnCategoryBeforeRemove',
        'OnCategoryBeforeSave',
        'OnCategoryRemove',
        'OnCategorySave',
        'OnChunkBeforeRemove',
        'OnChunkBeforeSave',
        'OnChunkFormDelete',
        'OnChunkFormPrerender',
        'OnChunkFormRender',
        'OnChunkFormSave',
        'OnChunkRemove',
        'OnChunkSave',
        'OnContextBeforeRemove',
        'OnContextBeforeSave',
        'OnContextFormPrerender',
        'OnContextFormRender',
        'OnContextRemove',
        'OnContextSave',
        'OnDocFormDelete',
        'OnDocFormPrerender',
        'OnDocFormRender',
        'OnDocFormSave',
        'OnDocPublished',
        'OnDocUnPublished',
        'OnEmptyTrash',
        'OnFileManagerUpload',
        'OnHandleRequest',
        'OnInitCulture',
        'OnLoadWebDocument',
        'OnLoadWebPageCache',
        'OnManagerAuthentication',
        'OnManagerLogin',
        'OnManagerLoginFormPrerender',
        'OnManagerLoginFormRender',
        'OnManagerLogout',
        'OnManagerPageInit',
        'OnPageNotFound',
        'OnPageUnauthorized',
        'OnParseDocument',
        'OnPluginBeforeRemove',
        'OnPluginBeforeSave',
        'OnPluginEventRemove',
        'OnPluginFormDelete',
        'OnPluginFormPrerender',
        'OnPluginFormRender',
        'OnPluginFormSave',
        'OnPluginRemove',
        'OnPluginSave',
        'OnPropertySetBeforeRemove',
        'OnPropertySetBeforeSave',
        'OnPropertySetRemove',
        'OnPropertySetSave',
        'OnResourceGroupBeforeRemove',
        'OnResourceGroupBeforeSave',
        'OnResourceGroupRemove',
        'OnResourceGroupSave',
        'OnResourceUndelete',
        'OnRichTextBrowserInit',
        'OnRichTextEditorInit',
        'OnRichTextEditorRegister',
        'OnSiteRefresh',
        'OnSiteSettingsRender',
        'OnUserActivate',
        'OnUserBeforeRemove',
        'OnUserBeforeSave',
        'OnUserChangePassword',
        'OnUserFormDelete',
        'OnUserFormSave',
        'OnUserNotFound',
        'OnUserRemove',
        'OnUserSave',
        'OnWebAuthentication',
        'OnWebLogin',
        'OnWebLogout',
        'OnWebPageComplete',
        'OnWebPageInit',
        'OnWebPagePrerender',
        'OnResourceDuplicate',
    );
    private $_events;

    public function __construct($scriptProperties = array()) {
        parent::__construct();
        $this->_scriptProperties = (array) $scriptProperties;
    }

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
     * This method will be called by modx event trigger.
     * This is where we call the current event handler.
     */
    final public function run() {
        $tstart = explode(' ', microtime());
        $tstart = $tstart[1] + $tstart[0];

        $event_name = $this->getEvent()->name;
        $return = $this->$event_name($this->getEvent()->params);

        $tstop = explode(' ', microtime());
        $tstop = $tstop[1] + $tstop[0];
        $duration = $tstop - $tstart;
        self::log(sprintf('Plugin: %s (%1.3f seconds)', $this->getName(), $duration), Zend_Log::DEBUG);
        if ($duration > 1) {
            self::log(sprintf('Inefficient Plugin Found: %s (%1.3f seconds)', $this->getName(), $duration), Zend_Log::WARN);
        }
        
        return $return;

        /**
         * @todo: Do not remove this line for now...
         */
//        self::getModX()->event->_output = true;
//        self::getModX()->event->output(true);
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
<?php
$o = new ' . get_class($this) . '($scriptProperties);
return $o->run();
';
        return $content;
        ;
    }

    /**
     * Sync plugin object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        self::log('Syncing Plugin: ' . $this->getName(), Zend_Log::INFO);

        /* @var $plugin modPlugin */
        $plugin = parent::_sync('modPlugin', array('name' => $this->getName()));

        // remove existing plugin events
        $pluginEvents = self::getModX()->getCollection('modPluginEvent', array('pluginid' => $plugin->get('id')));
        foreach ($pluginEvents as $pluginEvent) {
            $pluginEvent->remove();
        }

        // add new plugin events
        foreach ($this->getEvents() as $eventName => $setting) {
            $pluginEvent = self::getModX()->newObject('modPluginEvent');
            $pluginEvent->set('pluginid', $plugin->get('id'));
            $pluginEvent->set('event', $eventName);
            $pluginEvent->set('priority', intval($setting['priority']));
            $pluginEvent->set('priority', intval($setting['propertyset']));
            $pluginEvent->save();
        }
    }

    /**
     * Returns with defined Events
     * 
     * @return array
     */
    final private function getEvents() {
        if (null === $this->_events) {
            $this->_events = array();
            $methods = $this->getMethods();
            foreach ($methods as $method) {
                if (strpos(substr($method, 0, strlen(self::EVENT_METHOD_PREFIX)), self::EVENT_METHOD_PREFIX) !== false) {
                    $eventName = substr($method, strlen(self::EVENT_METHOD_PREFIX));
                    if (!in_array($eventName, $this->_available_events)) {
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

    protected function getMethods() {
        return get_class_methods($this);
    }

}