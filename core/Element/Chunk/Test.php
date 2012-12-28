<?php

namespace ModSync\Element\Chunk;

use ModSync;

class Test extends ModSync\Element\Chunk\ChunkAbstract {
    protected $_syncable = false;

    public function getContent() {
        return 'test';
    }

}