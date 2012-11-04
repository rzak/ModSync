<?php

namespace ModSync;

use DirectoryIterator;
use SplFileInfo;
use Zend;

require_once 'Zend/Loader/StandardAutoloader.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class Autoloader {

    public static function register() {
        /**
         * Register ModSync namespace
         */
        $autoLoader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
        $autoLoader->registerNamespace('ModSync', Base::getCoreComponentsDir() . DIRECTORY_SEPARATOR . __NAMESPACE__);
        $autoLoader->register();

        /**
         * Register all other namespaces
         */
        $components = new DirectoryIterator(Base::getCoreComponentsDir());
        foreach ($components as $component) {
            if ($component->isDot()) {
                continue;
            }
            if ($component->getFilename() == 'ModSync') {
                continue;
            }
            $componentFile = new SplFileInfo($component->getPathname() . DIRECTORY_SEPARATOR . 'Component' . DIRECTORY_SEPARATOR . 'Component.php');
            if ($componentFile->isFile()) {
                $autoLoader->registerNamespace($component->getFilename(), $component->getPathname());
            }
        }
    }

}