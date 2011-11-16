<?php

class ModSync_Autoloader
{

    public static function registerNamespaces()
    {
        $path = dirname(dirname(__FILE__));
        $components = scandir($path);
        foreach ($components as $component)
        {
            if (substr($component, 0, 1) == '.')
            {
                continue;
            }
            if (!file_exists($path . '/' . $component . '/' . ModSync_Component::SYNC_FLAG))
            {
                continue;
            }
            Zend_Loader_Autoloader::getInstance()->registerNamespace($component . '_');
        }
    }

}