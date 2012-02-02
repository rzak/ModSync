<?php

abstract class ModSync_Snippet_HookAbstract extends ModSync_Snippet_Abstract {

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
        ;
    }

}
