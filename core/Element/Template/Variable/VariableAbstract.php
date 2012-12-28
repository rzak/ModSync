<?php

namespace ModSync\Element\Template\Variable;

use ModSync;

abstract class VariableAbstract extends ModSync\Element\ElementAbstract implements ModSync\Element\Template\Variable\IsVariableInterface {

    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_DATE = 'date';
    const TYPE_DROPDOWN = 'listbox';
    const TYPE_EMAIL = 'email';
    const TYPE_FILE = 'file';
    const TYPE_AUTOTAG = 'autotag';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_IMAGE = 'image';
    const TYPE_LISTBOX_MULTIPLE = 'listbox-multiple';
    const TYPE_NUMBER = 'number';
    const TYPE_OPTION = 'option';
    const TYPE_RESOURCE_LIST = 'resourcelist';
    const TYPE_RICHTEXT = 'richtext';
    const TYPE_TAG = 'tag';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_URL = 'url';

    protected $_availableTypes = array(
        'text',
        'image',
        'dropdown',
    );
    protected $_input_properties = array();
    protected $_output_properties = array();
    protected $_type;
    protected $_caption;
    protected $_rank = 0;
    protected $_locked = 1;
    protected $_display = 'default';
    protected $_display_params;
    protected $_default_text;
    protected $_elements;
    protected $_source;

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
     * Returns tv's source id
     *
     * @return int
     */
    public function getSource() {
        if (null === $this->_source) {
            $this->_source = 0;
        }
        return $this->_source;
    }

    /**
     * Returns element's name
     *
     * @return string
     */
    final public function getName() {
        if (null === $this->_name) {
            $chunks = explode('_', str_replace('\\', '_', get_called_class()), 5);
            $this->_name = $chunks[0] . '_' . $chunks[4];
        }
        return $this->_name;
    }

//
//    /**
//     * Sets Property for element
//     *
//     * @param string $name
//     * @param string $value
//     * @param string $desc
//     * @param string $type
//     * @param array $options
//     * @param string $lexicon
//     */
//    final protected function setProperty($name, $value, $desc = '', $type = 'textfield', $options = array(), $lexicon = null) {
//        $this->_properties[$name] = array(
//            'name' => $name,
//            'desc' => $desc,
//            'type' => $type,
//            'options' => $options,
//            'value' => $value,
//            'lexicon' => $lexicon
//        );
//    }

    /**
     * Syncs an element with modx
     */
    final public function sync() {
        $modTemplateVar = parent::_sync('modTemplateVar', array('name' => $this->getName()));
        if ($this->getSource() > 0) {
            if (!$modTemplateVar) {
                $modTemplateVar = self::getModTemplateVar();
            }
            $sourceElements = self::getModX()->getCollection('sources.modMediaSourceElement', array(
                'object' => $modTemplateVar->get('id'),
                'object_class' => 'modTemplateVar',
                    ));

            /** @var modMediaSourceElement $sourceElement */
            foreach ($sourceElements as $sourceElement) {
                $sourceElement->remove();
            }


            $contextElements = self::getModX()->getCollection('modContext', array('key:!=' => 'mgr'));
            foreach ($contextElements as $context) {
                /** @var modMediaSourceElement $sourceElement */
                $sourceElement = self::getModX()->newObject('sources.modMediaSourceElement');
                $sourceElement->set('object', $modTemplateVar->get('id'));
                $sourceElement->set('object_class', 'modTemplateVar');
                $sourceElement->set('context_key', $context->get('key'));
                $sourceElement->set('source', $this->getSource());
                $sourceElement->save();
            }
        }
    }

    /**
     * Hook before save, returning false will abandon save
     * 
     * @param \modElement $modElement
     * @return boolean
     */
    public function onBeforeSave(\modElement &$modElement) {
        $modElement->set('type', $this->getType());
        $modElement->set('caption', $this->getCaption());
        $modElement->set('source', $this->getSource());
        $modElement->set('locked', intval($this->_locked));
        $modElement->set('rank', intval($this->_rank));
        $modElement->set('display', $this->_display);
        $modElement->set('display_params', $this->_display_params);
        $modElement->set('default_text', $this->_default_text);
        $modElement->set('elements', $this->_elements);
        $modElement->set('input_properties', $this->_input_properties);
        $modElement->set('output_properties', $this->_output_properties);
        $modElement->setProperties($this->_properties, true);
        return true;
    }

    /**
     * Returns modx element's field value
     *
     * @return \modTemplateVar
     */
    final static public function getModTemplateVar() {
        $class = get_called_class();
        $o = new $class();
        $modTemplateVar = self::getModX()->getObject('modTemplateVar', array('name' => $o->getName()));
        if (!$modTemplateVar) {
            $o->sync();
            return $o->getModTemplate();
        }
        return $modTemplateVar;
    }

}
