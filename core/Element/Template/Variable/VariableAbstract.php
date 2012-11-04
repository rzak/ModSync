<?php

namespace ModSync\Element\Template\Variable;

use ModSync;

abstract class VariableAbstract extends ModSync\Element\ElementAbstract implements ModSync\Element\Template\Variable\IsVariableInterface {

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
        parent::_sync('modTemplateVar', array('name' => $this->getName()));
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
     * @return modTemplateVar
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
