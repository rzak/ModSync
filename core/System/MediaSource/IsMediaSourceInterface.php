<?php

namespace ModSync\System\MediaSource;

use ModSync;

interface IsMediaSourceInterface extends ModSync\IsSyncableInterface {

    /**
     * Returns modMediaSource object
     *
     * @return \modMediaSource
     */
    static public function getModMediaSource();
}