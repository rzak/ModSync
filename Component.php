<?php

class ModSync_Component extends ModSync_Component_Abstract {
    const SYNC_FLAG = '.ModSync';
    const COMPONENT_FILE = 'Component.php';
    const COMPONENT_CLASS = '%s_Component';

    protected $_category = 'ModSync_Category_ModSync';
    protected $_syncable = true;

}