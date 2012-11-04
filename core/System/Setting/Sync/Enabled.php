<?php

namespace ModSync\System\Setting\Sync;

use ModSync;

class Enabled extends ModSync\System\Setting\SettingAbstract {

    protected $_value = true;
    protected $_xtype = self::TYPE_YESNO;

}