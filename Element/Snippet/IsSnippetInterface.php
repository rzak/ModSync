<?php

namespace ModSync\Element\Snippet;

use ModSync;

interface IsSnippetInterface extends ModSync\Element\IsElementInterface, ModSync\Element\Parameter\HasParameterInterface {

    /**
     * This is where the magic happens... modx snippet should always call this
     * method to execute the snippet.
     *
     * @return string
     */
    static public function process($args);

    /**
     * Process calls this method
     *
     * @return string
     */
    public function run();

    /**
     * Dispatch snippet 
     *
     * @return string
     */
    public function dispatch();

    /**
     * Before dispatch method
     *
     * @return void
     */
    public function beforeDispatch();

    /**
     * After dispatch method
     *
     * @return void
     */
    public function afterDispatch();
}