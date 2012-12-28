<?php

namespace ModSync\Element\Template;

use ModSync;

class Test extends ModSync\Element\Template\TemplateAbstract {

    protected $_syncable = false;

    public function getContent() {
        return 'test';
    }

}
