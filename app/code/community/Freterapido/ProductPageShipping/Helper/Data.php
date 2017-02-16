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
 * Module helper for translations and some layout staff
 *
 */
class Freterapido_ProductPageShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns module configuration model singleton
     *
     * @return Freterapido_ProductPageShipping_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('freterapido_productpageshipping/config');
    }
    
    /**
     * Retieve display positioning logic flag
     *
     * @return boolean
     */
    public function getDisplayPositionFlag()
    {
        return $this->_getConfig()->getDisplayPositionFlag();
    }
    
    /**
     * Retieve display positioning block, 
     * e.g. related sibling element for positioning
     *
     * @return string
     */
    public function getDisplayPositionBlock()
    {
        return $this->_getConfig()->getDisplayPositionBlock();
    }
    
}
