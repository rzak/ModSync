<?php

class ModSync_Plugin_ModSync extends ModSync_Plugin_Abstract {
    protected $_name = 'ModSync';

    protected function eventOnHandleRequest() {
        if (self::getModX()->context->get('key') == 'mgr') {
            return;
        }
        if (isset($_GET['ModClearCache'])) {
            $this->_doClearCache();
        }
        if (isset($_GET['ModSync'])) {
            $this->_doSync();
            $this->_doClearCache();
        }
        if (isset($_GET['firebugLite'])) {
            $this->_doFirebugLite();
        }
        return true;
    }

    private function _doFirebugLite() {
        self::getModX()->regClientStartupScript('https://getfirebug.com/firebug-lite.js');
    }

    private function _doClearCache() {
        self::log('Clearing Cache', Zend_Log::WARN);
        self::getModX()->getCacheManager()->clearCache();
    }

    private function _doSync() {
        self::log('------------------------------------', Zend_Log::DEBUG);
        self::log('SYNC ON LOCAL', Zend_Log::DEBUG);
        self::log('------------------------------------', Zend_Log::DEBUG);
        $path = MODX_CORE_PATH . 'components';
        $components = scandir($path);
        foreach ($components as $component) {
            if (substr($component, 0, 1) == '.') {
                continue;
            }
            if (!file_exists($path . '/' . $component . '/' . ModSync_Component::SYNC_FLAG)) {
                continue;
            }
            if (file_exists($path . '/' . $component . '/' . ModSync_Component::COMPONENT_FILE)) {
                $class = sprintf(ModSync_Component::COMPONENT_CLASS, $component);
                if (class_exists($class)) {
                    $o = new $class();
                    if (is_a($o, 'ModSync_Component_Abstract')) {
                        self::log('Syncing: ' . $o->getName(), Zend_Log::DEBUG);
                        $o->sync();
                    }
                }
            }
        }
    }

}