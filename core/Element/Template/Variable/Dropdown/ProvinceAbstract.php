<?php

namespace ModSync\Element\Template\Variable\Dropdown;

abstract class ProvinceAbstract extends DropdownAbstract {

    public static $PROVINCES = array(
        'AB' => 'Alberta',
        'BC' => 'British Columbia',
        'MB' => 'Manitoba',
        'NB' => 'New Brunswick',
        'NL' => 'Newfoundland and Labrador',
        'NS' => 'Nova Scotia',
        'NT' => 'Northwest Territories',
        'NU' => 'Nunavut',
        'ON' => 'Ontario',
        'PE' => 'Prince Edward Island',
        'QC' => 'Quebec',
        'SK' => 'Saskatchewan',
        'YT' => 'Yukon',
    );

    final public function getElements() {
        $out = array();
        foreach (self::$PROVINCES as $key => $name) {
            $out[] = sprintf('%s==%s', $name, $key);
        }
        return implode('||', $out);
    }

}
