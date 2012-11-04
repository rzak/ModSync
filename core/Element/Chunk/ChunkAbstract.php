<?php

namespace ModSync\Element\Chunk;

use ModSync;

abstract class ChunkAbstract extends ModSync\Element\ElementAbstract implements ModSync\Element\Chunk\IsChunkInterface {

    /**
     * Sync chunk object
     */
    final public function sync() {
        $this->_sync('modChunk', array('name' => $this->getName()));
    }

    /**
     * Returns object
     *
     * @param string $name
     * @return \modChunk
     * @throws ModSync\Element\Chunk\Exception
     */
    final public static function getModChunk($name) {
        $modChunk = self::getModX()->getObject('modChunk', array('name' => $name));
        if (!$modChunk) {
            throw new ModSync\Element\Chunk\Exception('Chunk `' . $name . '` does not exist');
        }
        return $modChunk;
    }

}
