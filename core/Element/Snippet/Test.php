<?php

namespace ModSync\Element\Snippet;

class Test extends SnippetAbstract {

    protected $_syncable = false;

    public function dispatch() {
        return 'testing 123';
    }

}
