<?php
/**
 * This plugin was based on EcomDev_ProductPageShipping <https://github.com/EcomDev/EcomDev_ProductPageShipping>
 *
 * @category  Freterapido
 * @package   Freterapido_ProductPageShipping
 * @author    freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license   http://opensource.org/licenses/MIT MIT
 * @link      https://github.com/freterapido/freterapido-magento
 */

/**
 * Display in position inserting logic source model
 *
 */
class Freterapido_ProductPageShipping_Model_Config_Source_Position_Flag
{
    /**
     * Returns after flag values for insert method
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('freterapido_productpageshipping')->__('Antes dos outros blocos'),
                'value' => 0
            ),
            array(
                'label' => Mage::helper('freterapido_productpageshipping')->__('Depois dos outros blocos'),
                'value' => 1
            )
        );
    }
}
