<?php

namespace ModSync\Element\Parameter;

use ModSync;

abstract class ParameterAbstract {

    protected $name;
    protected $value;
    protected $required;
    protected $default;
    protected $hasValue = false;
    protected $hasDefaultValue = false;

    function __construct($name, $required = false, $default = null) {
        $this->name = $name;
        $this->required = (boolean) $required;
        if (!is_null($default)) {
            $this->hasDefaultValue = true;
            $this->default = $default;
        }
        if ($this->required && $this->hasDefaultValue) {
            throw new ModSync\Element\Parameter\Exception('Parameter cannot be required with default value');
        }
    }

    function __toString() {
        throw new ModSync\Element\Parameter\Exception('Use getValue() method instead (' . $this->getName() . ')');
    }

    public function setValue($value = null) {
        if ($this->isRequired() && is_null($value)) {
            throw new ModSync\Element\Parameter\Exception('Required parameter (' . $this->getName() . ') not found');
        }
        $this->value = $value;
        $this->hasValue = true;
    }

    public function getValue() {
        if ($this->isRequired() && !$this->hasValue) {
            throw new ModSync\Element\Parameter\Exception('Parameter (' . $this->getName() . ') value not set');
        }
        if ($this->hasValue) {
            return $this->value;
        }
        if ($this->hasDefaultValue) {
            return $this->default;
        }
        return;
    }

    public function hasValue() {
        if ($this->hasValue || $this->hasDefaultValue) {
            return true;
        }
        return false;
    }

    public function getName() {
        return $this->name;
    }

    public function isRequired() {
        return $this->required;
    }

}
