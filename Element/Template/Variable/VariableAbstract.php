<?php

namespace ModSync\Element\Template\Variable;

use ModSync;

abstract class VariableAbstract extends ModSync\Base implements ModSync\Element\Template\Variable\IsVariableInterface {

    use ModSync\Element\Category\HasCategoryTrait;

    private $_properties = array();
    protected $_availableTypes = array(
        'text',
        'image',
        'dropdown'
    );
    protected $_input_properties = array();
    protected $_output_properties = array();
    protected $_syncable = true;
    protected $_id;
    protected $_name;
    protected $_type;
    protected $_caption;
    protected $_category;
    protected $_description;
    protected $_rank = 0;
    protected $_locked = 1;
    protected $_display = 'default';
    protected $_display_params;
    protected $_default_text;
    protected $_elements;

    /**
     * Returns tv's caption
     *
     * @return string
     */
    final public function getCaption() {
        if (null === $this->_caption) {
            $this->_caption = $this->getName();
        }
        return $this->_caption;
    }

    /**
     * Returns tv's type
     *
     * @return string
     */
    public function getType() {
        if (null === $this->_type) {
            $this->_type = 'text';
        }
        return $this->_type;
    }

    /**
     * Returns element's name
     *
     * @return string
     */
    final public function getName() {
        if (null === $this->_name) {
            $chunks = explode('_', get_class($this), 3);
            $this->_name = $chunks[0] . '_' . $chunks[2];
        }
        return $this->_name;
    }

    /**
     * Returns the description field
     *
     * @return string
     */
    final public function getDescription() {
        if (null === $this->_description) {
            $this->_description = 'Auto generated description for ' . $this->getName();
        }
        return $this->_description;
    }

    /**
     * Sets Property for element
     *
     * @param string $name
     * @param string $value
     * @param string $desc
     * @param string $type
     * @param array $options
     * @param string $lexicon
     */
    final protected function setProperty($name, $value, $desc = '', $type = 'textfield', $options = array(), $lexicon = null) {
        $this->_properties[$name] = array(
            'name' => $name,
            'desc' => $desc,
            'type' => $type,
            'options' => $options,
            'value' => $value,
            'lexicon' => $lexicon
        );
    }

    /**
     * Syncs an element with modx
     */
    final public function sync() {
        if (!$this->isSyncable()) {
            return;
        }

        ModSync\Logger::info('Syncing Variable: ' . $this->getName());

        /* @var $modxElement \modTemplateVar */
        if (!$modxElement = self::getModX()->getObject('modTemplateVar', array('name' => $this->getName()))) {
            $modxElement = self::getModX()->newObject('modTemplateVar', array('name' => $this->getName()));
            $this->onInsert();
        } else {
            $this->onUpdate();
        }

        if (null === $modxElement) {
            throw new Exception('Failed to sync template variable: ' . $this->getName());
        }
        if ($this->hasCategory()) {
            $modxElement->set('category', $this->getCategory()->getId());
        }
        $modxElement->set('type', $this->getType());
        $modxElement->set('caption', $this->getCaption());
        $modxElement->set('description', $this->getDescription());
        $modxElement->set('locked', intval($this->_locked));
        $modxElement->set('rank', intval($this->_rank));
        $modxElement->set('display', $this->_display);
        $modxElement->set('display_params', $this->_display_params);
        $modxElement->set('default_text', $this->_default_text);
        $modxElement->set('elements', $this->_elements);
        $modxElement->set('input_properties', $this->_input_properties);
        $modxElement->set('output_properties', $this->_output_properties);
        $modxElement->setProperties($this->_properties, true);
        $modxElement->save();
    }

    /**
     * Executes on insert
     */
    public function onInsert() {
        $this->setProperty('modsync_syncable', '1', 'Determines if this element has been synced using ModSync plugin', 'combo-boolean');
        $this->setProperty('modsync_last_synced', date('Y-m-d H:i:s'), 'This element was last synced on this date');
    }

    /**
     * Executes on update
     */
    public function onUpdate() {
        $this->setProperty('modsync_last_synced', date('Y-m-d H:i:s'), 'This element was last synced on this date');
    }

    /**
     * Is this item syncable?
     *
     * @return boolean
     */
    public function isSyncable() {
        return (bool) $this->_syncable;
    }

    /**
     * Returns modx element's field value
     *
     * @param string $name
     * @return mixed
     */
    final public function get($name) {
        if (!$modxElement = self::getModX()->getObject('modTemplateVar', array('name' => $this->getName()))) {
            $this->sync();
            return $this->get($name);
        }
        return $modxElement->get($name);
    }

}
