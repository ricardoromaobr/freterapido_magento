<?php
/**
 * This plugin was based on EcomDev_ProductPageShipping <https://github.com/EcomDev/EcomDev_ProductPageShipping>
 *
 * @category  Freterapido
 * @package   Freterapido_ProductPageShipping
 * @author    freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license   https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 * @link      https://github.com/freterapido/freterapido-magento
 */

/**
 * Abstract block for estimate module
 *
 */
abstract class Freterapido_ProductPageShipping_Block_Estimate_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Estimate model
     *
     * @var Freterapido_ProductPageShipping_Model_Estimate
     */
    protected $_estimate = null;


    /**
     * Config model
     *
     * @var Freterapido_ProductPageShipping_Model_Config
     */
    protected $_config = null;


    /**
     * Module session model
     *
     * @var $_session Freterapido_ProductPageShipping_Model_Session
     */
    protected $_session = null;

    /**
     * List of carriers
     *
     * @var array
     */
    protected $_carriers = null;

    /**
     * Retrieve estimate data model
     *
     * @return Freterapido_ProductPageShipping_Model_Estimate
     */
    public function getEstimate()
    {
        if ($this->_estimate === null) {
            $this->_estimate = Mage::getSingleton('freterapido_productpageshipping/estimate');
        }

        return $this->_estimate;
    }

    /**
     * Retrieve configuration model for module
     *
     * @return Freterapido_ProductPageShipping_Model_Config
     */
    public function getConfig()
    {
        if ($this->_config === null) {
            $this->_config = Mage::getSingleton('freterapido_productpageshipping/config');
        }

        return $this->_config;
    }

    /**
     * Retrieve session model object
     *
     * @return Freterapido_ProductPageShipping_Model_Session
     */
    public function getSession()
    {
        if ($this->_session === null) {
            $this->_session = Mage::getSingleton('freterapido_productpageshipping/session');
        }

        return $this->_session;
    }

    /**
     * Check is enabled functionality
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getConfig()->isEnabled() && !$this->getProduct()->isVirtual();
    }
}
