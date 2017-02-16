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
 * Module observer
 *
 */
class Freterapido_ProductPageShipping_Model_Observer
{
    /**
     * Config model
     *
     * @var Freterapido_ProductPageShipping_Model_Config
     */
    protected $_config = null;

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
     * Layouts initializations observer,
     * add the form block into the position that was specified by the configuration
     *
     * @param Varien_Event_Observer $observer
     */
    public function observeLayoutHandleInitialization(Varien_Event_Observer $observer)
    {
        /* @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getAction();
        $fullActionName = $controllerAction->getFullActionName();
        if ($this->getConfig()->isEnabled() && in_array($fullActionName, $this->getConfig()->getControllerActions())) {
            $position = $this->getConfig()->getDisplayPosition();
            $layoutHandle = $this->getConfig()->getPositionSource()->getLayoutHandleName($position);
            if ($layoutHandle) {
                // Apply shipping estimator position layout handle
                $controllerAction->getLayout()->getUpdate()->addHandle(
                    $layoutHandle
                );
            }
        }
    }
}
