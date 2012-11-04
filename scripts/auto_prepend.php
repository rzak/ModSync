<?php
/**
 * Suggested modx file structore is:
 * 
 * /domain/
 * /domain/apache/domain.conf
 * /domain/git/ModSync/
 * /domain/svn/
 * /domain/log/
 * /domain/log/error.log
 * /domain/log/access.log
 * /domain/log/modsync.log
 * /domain/web/
 * /domain/web/config/
 * /domain/web/config/auto_prepend.php
 * /domain/web/config/config.inc.php (symlink to /domain/web/core/config/config.inc.php)
 * /domain/web/core/
 * /domain/web/core/components/
 * /domain/web/core/components/ModSync/ (symlink to /domain/git/ModSync)
 * /domain/web/public/
 * /domain/web/public/assets/
 * /domain/web/public/assets/components/
 * /domain/web/public/manager/
 * /domain/web/public/connectors/
 * 
 */


/**
 * This file will be automatically loaded on every php load
 */
define('__WEB_ROOT_DIR__', dirname(dirname(__FILE__)));

/**
 * Add Include paths for devlin and application elements
 */
set_include_path(get_include_path() . PATH_SEPARATOR . __WEB_ROOT_DIR__ . '/core/components');
set_include_path(get_include_path() . PATH_SEPARATOR . __WEB_ROOT_DIR__ . '/lib');

require_once 'ModSync/Autoloader.php';
\ModSync\Autoloader::register();
