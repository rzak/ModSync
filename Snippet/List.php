<?php

class ModSync_Snippet_List extends ModSync_Snippet_Abstract {

//    protected $_name = 'List';

    protected function defineParams() {
        $this->addParam(new ModSync_Parameter_String('openTemplate', false, '<ul>'));
        $this->addParam(new ModSync_Parameter_String('closeTemplate', false, '</ul>'));
        $this->addParam(new ModSync_Parameter_String('itemTemplate', false, '<li><a class="[[*class]]" href="[[*url]]">[[*menutitle]]</a></li>'));
    }

    protected function dispatch() {
        $out = $this->getParam('openTemplate')->getValue();
        foreach ($this->getList() as $item) {
            $out .= $this->personalizeItem($item);
        }
        $out .= $this->getParam('closeTemplate')->getValue();
        return $out;
    }

    protected function getList() {
        return self::getModX()->getCollection('modResource', array('parent' => '0'));
    }

    protected function personalizeItem($item) {
        $out = $this->getParam('itemTemplate')->getValue();
        $out = str_replace('[[*menutitle]]', $item->get('menutitle'), $out);
        $out = str_replace('[[*url]]', self::getModX()->makeUrl($item->get('id')), $out);
        $out = str_replace('[[*class]]', '', $out);
        return $out;
    }

}