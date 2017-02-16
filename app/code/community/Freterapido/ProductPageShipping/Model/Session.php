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
 * Session model
 *
 * Using for saving the form data between estimations
 *
 */
class Freterapido_ProductPageShipping_Model_Session extends Mage_Core_Model_Session_Abstract
{
    const SESSION_NAMESPACE = 'productpageshipping';

    public function __construct()
    {
        $this->init(self::SESSION_NAMESPACE);
    }
}
