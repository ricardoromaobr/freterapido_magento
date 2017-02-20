<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 */

class Freterapido_Freterapido_Model_Source_LimitOptions
{
    /**
     * Get options for weight
     *
     * @return array
     */
    public function toOptionArray()
    {

        $array = [];

        foreach (range(1, 20) as $number) {
            $array[$number] = array('value' => $number, 'label' => Mage::helper('adminhtml')->__((string)$number));
        }

        return $array;

    }
}
