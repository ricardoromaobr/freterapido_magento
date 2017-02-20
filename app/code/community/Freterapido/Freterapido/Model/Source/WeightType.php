<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 */

class Freterapido_Freterapido_Model_Source_WeightType
{

    // Get the constants of Kilos or Grams
    const WEIGHT_IN_KG = 'kg';
    const WEIGHT_IN_GR = 'gr';

    /**
     * Get the options of weight
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::WEIGHT_IN_KG, 'label' => Mage::helper('adminhtml')->__('Quilos')),
            array('value' => self::WEIGHT_IN_GR, 'label' => Mage::helper('adminhtml')->__('Gramas')),
        );
    }
}