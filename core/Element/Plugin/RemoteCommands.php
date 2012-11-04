<?php

namespace ModSync\Element\Plugin;

use ModSync;

class RemoteCommands extends ModSync\Element\Plugin\PluginAbstract {

    protected $_syncable = false;

    protected function eventOnWebPageComplete() {
        /* define the IP of the master instance which does not need to execute remote commands */
        $master_instance = self::getModX()->getOption('master_instance', $this->_scriptProperties, '127.0.0.1');

        /* get the instance IP */
        $instance = $_SERVER['SERVER_ADDR'];

        /* the number of seconds the remote command is valid for */
        $seconds = 1440;

        /* find any remote commands to execute from the master instance */
        if (!empty($instance) && self::getModX()->getService('registry', 'registry.modRegistry') && $instance !== $master_instance) {
            self::getModX()->registry->addRegister('remotes', 'registry.modDbRegister', array('directory' => 'remotes'));
            self::getModX()->registry->remotes->connect();

            /* if not already registered, register this instance for $seconds */
            self::getModX()->registry->remotes->subscribe("/distrib/instances/{$instance}");
            $registration = self::getModX()->registry->remotes->read(array('poll_limit' => 1, 'msg_limit' => 1, 'remove_read' => false));
            self::getModX()->registry->remotes->unsubscribe("/distrib/instances/{$instance}");
            if (empty($registration) || !reset($registration)) {
                self::getModX()->registry->remotes->subscribe("/distrib/instances/");
                self::getModX()->registry->remotes->send("/distrib/instances/", array($instance => "{$instance}"), array('ttl' => $seconds));
                self::getModX()->registry->remotes->unsubscribe("/distrib/instances/");
            }

            /* find any valid command messages for this instance and act on them */
            self::getModX()->registry->remotes->subscribe("/distrib/commands/{$instance}/");
            $commands = self::getModX()->registry->remotes->read(array('poll_limit' => 1, 'msg_limit' => 1));
            self::getModX()->registry->remotes->unsubscribe("/distrib/commands/{$instance}/");
            if (!empty($commands)) {
                $command = reset($commands);
                switch ($command) {
                    case 'clearCache':
                        self::getModX()->cacheManager->refresh();
                        break;
                    default:
                        break;
                }
            }
        }
        return;
    }

    protected function eventOnSiteRefresh() {
        /* read instances and write clear cache msg to each command directory */
        if (self::getModX()->getService('registry', 'registry.modRegistry')) {
            self::getModX()->registry->addRegister('remotes', 'registry.modDbRegister', array('directory' => 'remotes'));
            self::getModX()->registry->remotes->connect();
            self::getModX()->registry->remotes->subscribe('/distrib/instances/');
            $instances = self::getModX()->registry->remotes->read(array('poll_limit' => 1, 'msg_limit' => 25, 'remove_read' => false));
            if (!empty($instances)) {
                foreach ($instances as $instance) {
                    if ($instance == $_SERVER['SERVER_ADDR'])
                        continue;
                    self::getModX()->registry->remotes->subscribe("/distrib/commands/{$instance}/");
                    self::getModX()->registry->remotes->send("/distrib/commands/{$instance}/", 'clearCache', array('expires' => time() + 1440));
                }
            }
        }
    }

}