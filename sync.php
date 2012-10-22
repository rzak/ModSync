<?php
if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', false);
}
require_once '../../web/config/auto_prepend.php';
@include(\ModSync\Base::getCoreDir() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.core.php');
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', \ModSync\Base::getCoreDir() . DIRECTORY_SEPARATOR);
$_SERVER['LOG_PRIORITY'] = 7;
$_SERVER['LOG_FILE'] = 'web.log';


/* include the modX class */
if (!@include_once (MODX_CORE_PATH . "model/modx/modx.class.php")) {
    $errorMessage = 'Site temporarily unavailable';
    @include(MODX_CORE_PATH . 'error/unavailable.include.php');
    header('HTTP/1.1 503 Service Unavailable');
    echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
    exit();
}

/* start output buffering */
ob_start();

/* Create an instance of the modX class */
$modx= new modX();
if (!is_object($modx) || !($modx instanceof modX)) {
    @ob_end_flush();
    $errorMessage = '<a href="setup/">MODX not installed. Install now?</a>';
    @include(MODX_CORE_PATH . 'error/unavailable.include.php');
    header('HTTP/1.1 503 Service Unavailable');
    echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
    exit();
}

/* Initialize the default 'web' context */
$modx->initialize('web');
$component = new ModSync\Component\Component;
$component->sync();
echo 'done';
