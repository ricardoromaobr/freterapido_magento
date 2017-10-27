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
 * Estimte form block
 *
 * Displays fields to enter for estimation of the shipping rate
 *
 */
class Freterapido_ProductPageShipping_Block_Estimate_Form extends Freterapido_ProductPageShipping_Block_Estimate_Abstract
{
    /**
     * Check is field visible in the form
     *
     * If config model have method like useFieldName,
     * method uses it to check field visibility, otherwise returns true
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isFieldVisible($fieldName)
    {
        if (method_exists($this->getConfig(), 'use' . uc_words($fieldName, ''))) {
            return $this->getConfig()->{'use' . uc_words($fieldName, '')}();
        }

        return true;
    }

    /**
     * Retrieve field value
     *
     * @param string $fieldName
     * @return mixed
     */
    public function getFieldValue($fieldName)
    {
        $values = $this->getSession()->getFormValues();

        if (isset($values[$fieldName])) {
            return $values[$fieldName];
        }

        return null;
    }

    /**
     * Retireve url for estimation form submitting
     *
     * @return string
     */
    public function getEstimateUrl()
    {
        return $this->getUrl('freterapido_productpageshipping/estimate/estimate', array('_current' => true));
    }

    /**
     * Retrieve available carriers
     *
     * @return array
     */
    public function getCarriers()
    {
        if ($this->_carriers === null) {
            $this->_carriers = Mage::getModel('shipping/config')->getActiveCarriers();
        }

        return $this->_carriers;
    }

    /**
     * Check is field required or not
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isFieldRequired($fieldName)
    {
        $methodMap = array(
            'postcode' => 'isZipCodeRequired' // Checks is postal code required
        );

        if (!isset($methodMap[$fieldName])) {
            return false;
        }

        $method = $methodMap[$fieldName];
        foreach ($this->getCarriers() as $carrier) {
            if (method_exists($carrier, $method) && $carrier->$method()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check is required usage of shopping cart items
     * in shipping estimate
     *
     * @return boolean
     */
    public function useShoppingCart()
    {
        if ($this->getSession()->getFormValues() === null || !$this->isFieldVisible('cart')) {
            return $this->getConfig()->useCartDefault();
        }

        return $this->getFieldValue('cart');
    }
}
