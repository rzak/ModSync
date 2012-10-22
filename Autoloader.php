<?php

namespace ModSync;

use DirectoryIterator;
use SplFileInfo;
use Zend;

require_once 'Zend/Loader/StandardAutoloader.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class Autoloader {

    public static function register() {
        $path = __WEB_ROOT_DIR__ . '/core/components';

        /**
         * Instantiate the autoloader and register devlin / app namespaces
         */
        $autoLoader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
        $autoLoader->registerNamespace('ModSync', Base::getCoreComponentsDir() . DIRECTORY_SEPARATOR . __NAMESPACE__);
        $autoLoader->register();

        $components = new DirectoryIterator($path);
        foreach ($components as $component) {
            if ($component->isDot()) {
                continue;
            }
            $componentFile = new SplFileInfo($component->getPathname() . DIRECTORY_SEPARATOR . 'Component' . DIRECTORY_SEPARATOR . 'Component.php');
            if ($componentFile->isFile()) {
                $autoLoader->registerNamespace($component->getFilename(), $component->getPathname());
            }
        }
    }

}