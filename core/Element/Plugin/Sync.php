<?php

namespace ModSync\Element\Plugin;

use DirectoryIterator;
use FilesystemIterator;
use SplFileInfo;
use ReflectionClass;
use LogicException;
use ReflectionException;
use ModSync;

class Sync extends ModSync\Element\Plugin\PluginAbstract {

    protected function eventOnHandleRequest() {
        if (self::getModX()->context->get('key') == 'mgr') {
            return;
        }
        $enabled = self::getModX()->getOption('modsync__sync_enabled', null, true);
        if (!$enabled) {
            return;
        }
        $triggerKey = self::getModX()->getOption('modsync__sync_triggerkey', null, 'ModSync');
        $triggerValue = self::getModX()->getOption('modsync__sync_triggervalue', null, 'doit');
        if (isset($_GET[$triggerKey]) && $_GET[$triggerKey] == $triggerValue) {
            $this->_doSync();
            $this->_doClearCache();
        }
        return;
    }

//    private function _doFirebugLite() {
//        self::getModX()->regClientStartupScript('https://getfirebug.com/firebug-lite.js');
//    }
//
    private function _doClearCache() {
        ModSync\Logger::notice('Clearing Cache');
        self::getModX()->getCacheManager()->refresh();
    }

    private function _doSync() {
        $it = new DirectoryIterator(self::getCoreComponentsDir());
        foreach ($it as $dir) {
            if (!$dir->isDot() && $dir->isDir()) {
                $componentFile = new SplFileInfo($dir->getPathname() . DIRECTORY_SEPARATOR . 'Component' . DIRECTORY_SEPARATOR . 'Component.php');
                if ($componentFile->isFile()) {
                    try {
                        $class = new ReflectionClass('\\' . $dir->getFilename() . '\\Component\\Component');
                        if (!$class->isAbstract() && !$class->isInterface() && !$class->isTrait() && $class->implementsInterface('ModSync\Component\IsComponentInterface')) {
                            $o = $class->newInstance();
                            if ($o->isSyncable()) {
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
            }
        }
    }

}