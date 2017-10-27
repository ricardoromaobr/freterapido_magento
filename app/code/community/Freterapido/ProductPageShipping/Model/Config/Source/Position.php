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
 * Display in field options source model
 *
 */
class Freterapido_ProductPageShipping_Model_Config_Source_Position
{
    /**
     * Shipping Estimator Positions Map
     *
     * @var Varien_Object
     */
    protected static $_positions = null;

    /**
     * Returns list of possible options
     *
     * @return Varien_Object
     */
    public static function getPositions()
    {
        if (self::$_positions === null) {
            self::_initPositions();
        }

        return self::$_positions;
    }

    /**
     * Initializes possible positions for shipping estimator
     *
     */
    protected static function _initPositions()
    {
        $positions =  new Varien_Object(array(
            Freterapido_ProductPageShipping_Model_Config::DISPLAY_POSITION_LEFT => array(
                'label' => Mage::helper('freterapido_productpageshipping')->__('Coluna da esquerda'),
                'handle' => Freterapido_ProductPageShipping_Model_Config::LAYOUT_HANDLE_LEFT
            ),
            Freterapido_ProductPageShipping_Model_Config::DISPLAY_POSITION_RIGHT => array(
                'label' => Mage::helper('freterapido_productpageshipping')->__('Coluna da direita'),
                'handle' => Freterapido_ProductPageShipping_Model_Config::LAYOUT_HANDLE_RIGHT
            ),
            Freterapido_ProductPageShipping_Model_Config::DISPLAY_POSITION_ADDITIONAL => array(
                'label' => Mage::helper('freterapido_productpageshipping')->__('Bloco de informação adicional'),
                'handle' => Freterapido_ProductPageShipping_Model_Config::LAYOUT_HANDLE_ADDITIONAL
            ),
            Freterapido_ProductPageShipping_Model_Config::DISPLAY_POSITION_CUSTOM => array(
                'label' => Mage::helper('freterapido_productpageshipping')->__('Layout personalizado'),
                'handle' => Freterapido_ProductPageShipping_Model_Config::LAYOUT_HANDLE_CUSTOM
            )
        ));

        Mage::dispatchEvent('freterapido_productpageshipping_config_source_position_init', array('positions' => $positions));

        self::$_positions = $positions;
    }

    /**
     * Returns layout handle name for a shipping estimator position
     *
     * @param string $position
     * @return string
     */
    public function getLayoutHandleName($position)
    {
        if (($handle = self::getPositions()->getData($position . '/handle'))) {
            return $handle;
        }

        return false;
    }

    /**
     * Return list of options for the system configuration field.
     * These options indicate the position of the form block on the page
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        foreach (array_keys(self::getPositions()->getData()) as $position) {
            $options[] = array(
                'value' => $position,
                'label' => self::getPositions()->getData($position . '/label')
            );
        }

        return $options;
    }
}
