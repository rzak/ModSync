<?php

namespace ModSync\Element\Plugin;

use ModSync;

interface IsPluginInterface extends ModSync\Element\IsElementInterface, ModSync\Element\HasProfilingInterface {

    /**
     * This is where the magic happens... modx snippet should always call this
     * method to execute the snippet.
     *
     * @return string
     */
    static public function process($args);

    /**
     * Disable plugin
     * 
     * @param string $name 
     */
    static public function disable($name);

    /**
     * This method will be called by modx event trigger.
     * This is where we call the current event handler.
     */
    public function run();

    /**
     * Returns the current Event
     * 
     * @return \modSystemEvent
     */
    public function getEvent();
}