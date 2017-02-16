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
 * Module configuration model
 *
 */
class Freterapido_ProductPageShipping_Model_Config
{
    /**
     * A configuration path for the module active state setting
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'freterapido_productpageshipping/settings/enabled';

    /**
     * A configuration path for the postcode field usage
     *
     * @var string
     */
    const XML_PATH_USE_POSTCODE = 'freterapido_productpageshipping/settings/use_postcode';

    /**
     * A configuration path for the coupon code field usage
     *
     * @var string
     */
    const XML_PATH_USE_COUPON_CODE = 'freterapido_productpageshipping/settings/use_coupon_code';

    /**
     * A configuration path for the include shopping cart checkbox visibility
     *
     * @var string
     */
    const XML_PATH_USE_CART = 'freterapido_productpageshipping/settings/use_cart';

    /**
     * A configuration path for the default store country usage
     *
     * @var string
     */
    const XML_PATH_DEFAULT_COUNTRY = 'shipping/origin/country_id';


    /**
     * A configuration path for the list of layout handles for displaying of estimate form
     *
     * @var string
     */
    const XML_PATH_CONTROLLER_ACTIONS = 'freterapido/productshippingpage/controller_actions';

    /**
     * A configuration paths for the display position on the page
     *
     * @var string
     */
    const XML_PATH_DISPLAY_POSITION = 'freterapido_productpageshipping/settings/display_position';
    const XML_PATH_DISPLAY_POSITION_FLAG = 'freterapido_productpageshipping/settings/display_position_flag';
    const XML_PATH_DISPLAY_POSITION_BLOCK = 'freterapido_productpageshipping/settings/display_position_block';

    /**
     * Display positions for shipping estimation form
     * @var string
     */
    const DISPLAY_POSITION_RIGHT = 'right';
    const DISPLAY_POSITION_LEFT = 'left';
    const DISPLAY_POSITION_ADDITIONAL = 'additional';
    const DISPLAY_POSITION_CUSTOM = 'custom';

    /**
     * Layout handles names for applying of positions
     *
     * @var string
     */
    const LAYOUT_HANDLE_LEFT = 'freterapido_productpageshipping_left';
    const LAYOUT_HANDLE_RIGHT = 'freterapido_productpageshipping_right';
    const LAYOUT_HANDLE_ADDITIONAL = 'freterapido_productpageshipping_additional';
    const LAYOUT_HANDLE_CUSTOM = 'freterapido_productpageshipping_custom';

    /**
     * Retrive a configuration flag for the postal code field usage in the estimate
     *
     * @return boolean
     */
    public function usePostcode()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_POSTCODE);
    }

    /**
     * Retrive a configuration flag for the coupon code field usage in the estimate
     *
     * @return boolean
     */
    public function useCouponCode()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_COUPON_CODE);
    }

    /**
     * Retrive default country
     *
     * @return string
     */
    public function getDefaultCountry()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_COUNTRY);
    }

    /**
     * Check the module active state configuration
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Retieve display type configuration value
     *
     * @return string
     */
    public function getDisplayPosition()
    {
        return Mage::getStoreConfig(self::XML_PATH_DISPLAY_POSITION);
    }
    
    
    /**
     * Retieve display positioning logic flag
     *
     * @return boolean
     */
    public function getDisplayPositionFlag()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_POSITION_FLAG);
    }
    
    /**
     * Retieve display positioning block, 
     * e.g. related sibling element for positioning
     *
     * @return string
     */
    public function getDisplayPositionBlock()
    {
        return Mage::getStoreConfig(self::XML_PATH_DISPLAY_POSITION_BLOCK);
    }
    
    /**
     * Returns position source model
     * 
     * @return Freterapido_ProductPageShipping_Model_Config_Source_Position
     */
    public function getPositionSource()
    {
        return Mage::getSingleton('freterapido_productpageshipping/config_source_position');
    }

    /**
     * Retrieve layout handles list for applying of the form
     *
     * @return array
     */
    public function getControllerActions()
    {
        $actions = array();
        foreach (Mage::getConfig()->getNode(self::XML_PATH_CONTROLLER_ACTIONS)->children() as $action => $node) {
            $actions[] = $action;
        }

        return $actions;
    }
}
