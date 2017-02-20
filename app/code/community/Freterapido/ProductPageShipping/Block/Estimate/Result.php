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
 * Estiamtion results block
 *
 *
 */
class Freterapido_ProductPageShipping_Block_Estimate_Result extends Freterapido_ProductPageShipping_Block_Estimate_Abstract
{
    /**
     * Retrieves result from estimate model
     *
     * @return array|null
     */
    public function getResult()
    {
        return $this->getEstimate()->getResult();
    }

    /**
     * Check result existance
     *
     * @return boolean
     */
    public function hasResult()
    {
        return $this->getResult() !== null;
    }

    /**
     * Retrieve carrier name for shipping rate group
     *
     * @param string $code
     * @return string|null
     */
    public function getCarrierName($code)
    {
        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($code);
        if ($carrier) {
            return $carrier->getConfigData('title');
        }

        return null;
    }

    /**
     * Retrieve shipping price for current address and rate
     *
     * @param decimal $price
     * @param boolean $flag show include tax price flag
     * @return string
     */
    public function getShippingPrice($price, $flag)
    {
        return $this->formatPrice(
            $this->helper('tax')->getShippingPrice(
                $price,
                $flag,
                $this->getEstimate()
                    ->getQuote()
                    ->getShippingAddress()
           )
        );
    }

    /**
     * Format price value depends on store settings
     *
     * @param decimal $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getEstimate()
            ->getQuote()
            ->getStore()
            ->convertPrice($price, true);
    }
}
