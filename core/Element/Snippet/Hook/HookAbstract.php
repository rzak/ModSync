<?php

namespace ModSync\Element\Snippet\Hook;

use ModSync\Element\Snippet;

abstract class HookAbstract extends Snippet\SnippetAbstract {

    /**
     * FormIt Hooks
     * 
     * @var fiHooks
     */
    protected $_hook;

    public function __construct(&$hook = null) {
        parent::__construct();
//        unset($hook->modx);
//        unset($hook->formit);
        $this->_hook = $hook;
    }

    /**
     * Returns the snippet content
     * 
     * @return string
     */
    final public function getContent() {
        $content = '
<?php
$o = new ' . get_class($this) . '($hook);
return $o->run();
';
        return $content;
    }

}
