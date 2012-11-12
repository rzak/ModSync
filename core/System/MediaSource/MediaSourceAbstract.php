<?php

namespace ModSync\System\MediaSource;

use ModSync;

abstract class MediaSourceAbstract extends ModSync\Base implements ModSync\System\MediaSource\IsMediaSourceInterface {

    const CLASS_FILE_SYSTEM = 'sources.modFileMediaSource';
    const CLASS_AMAZON_S3 = 'sources.modS3MediaSource';

    protected $_supported_media_sources = array(
        self::CLASS_FILE_SYSTEM
    );
    protected $_syncable = true;
    protected $_name;
    protected $_description;
    protected $_class_key;
    protected $_properties = array(
        'basePath' => array(
            'name' => 'basePath',
            'desc' => 'prop_file.basePath_desc',
            'type' => 'textfield',
            'options' => array(),
            'value' => '',
            'lexicon' => 'core:source'
        ),
        'baseUrl' => array(
            'name' => 'baseUrl',
            'desc' => 'prop_file.baseUrl_desc',
            'type' => 'textfield',
            'options' => array(),
            'value' => '',
            'lexicon' => 'core:source'
        ),
    );
    protected $_is_stream = true;

    /**
     * Setup url
     * 
     * @return string
     */
    abstract public function getUrl();

    /**
     * Returns element's name
     *
     * @return string
     */
    public function getName() {
        if (null === $this->_name) {
            $chunks = explode('_', str_replace('\\', '_', get_called_class()), 4);
            $this->_name = $chunks[0] . '_' . $chunks[3];
        }
        return $this->_name;
    }

    /**
     * Returns element's name
     *
     * @return string
     */
    public function getDescription() {
        if (null === $this->_description) {
            $this->_description = $this->getName();
        }
        return $this->_description;
    }

    /**
     * Returns class_key for media source
     * 
     * @return string
     * @throws ModSync\System\MediaSource\Exception
     */
    public function getClassKey() {
        if (null === $this->_class_key) {
            $this->_class_key = self::CLASS_FILE_SYSTEM;
        }
        if (!in_array($this->_class_key, $this->_supported_media_sources)) {
            throw new ModSync\System\MediaSource\Exception(sprintf('Media source `%s` not supported', $this->_class_key));
        }
        return $this->_class_key;
    }

    /**
     * Sync media source
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }
        
        /* @var $modElement \modMediaSource */
        $modElement = self::getModX()->getObject('sources.modMediaSource', array('name' => $this->getName()));
        if ($modElement) {
            ModSync\Logger::debug('Already exists: ' . get_called_class());
        } else {
            ModSync\Logger::info('Inserting: ' . get_called_class());
            $modElement = self::getModX()->newObject('sources.modMediaSource');
            $modElement->set('name', $this->getName());
            $modElement->set('description', $this->getDescription());
            $modElement->set('class_key', $this->getClassKey());
            $this->_properties['basePath']['value'] = 'assets' . DIRECTORY_SEPARATOR . $this->getUrl();
            $this->_properties['baseUrl']['value'] = 'assets' . DIRECTORY_SEPARATOR . $this->getUrl();
            $modElement->setProperties($this->_properties);
            @mkdir(self::getAssetsDir() . DIRECTORY_SEPARATOR . trim($this->getUrl(), '/'), 0755, true);
            $this->onInsert();
            $modElement->save();
        }
    }

    public function onInsert() {
        
    }

    public function onUpdate() {
        
    }

    /**
     * Is this item syncable?
     *
     * @return boolean
     */
    final public function isSyncable() {
        return (bool) $this->_syncable;
    }

    /**
     * Returns modMediaSource object
     *
     * @return \modMediaSource
     */
    final static public function getModMediaSource() {
        $class = get_called_class();
        $o = new $class();

//        if (null === self::getModX()->newObject($o->getClassKey())) {
//            self::getModX()->addPackage('sources', self::getCoreDir() . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'modx' . DIRECTORY_SEPARATOR);
//        }

        $modMediaSource = self::getModX()->getObject('sources.modMediaSource', array('name' => $o->getName()));
        if (!$modMediaSource) {
            $o->sync();
            return $o::getModMediaSource();
        }
        return $modMediaSource;
    }

}