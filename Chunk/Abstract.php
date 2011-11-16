<?php

abstract class ModSync_Chunk_Abstract extends ModSync_Element {

    /**
     * Sync chunk object
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        self::log('Syncing Chunk: ' . $this->getName(), Zend_Log::INFO);
        $chunk = parent::_sync('modChunk', array('name' => $this->getName()));
    }

    /**
     * Returns object
     *
     * @param string $name
     * @return modChunk
     */
    final public static function getModChunk($name) {
        if (!$modChunk = self::getModX()->getObject('modChunk', array('name' => $name))) {
            throw new ModSync_Exception('Chunk "' . $name . '" does not exist');
        }
        return $modChunk;
    }

}
