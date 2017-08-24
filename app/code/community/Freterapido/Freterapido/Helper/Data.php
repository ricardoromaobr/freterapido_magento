<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 */

class Freterapido_Freterapido_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CODE = 'freterapido';

    protected $_code = self::CODE;

    /**
     * Gets the configuration value by path
     *
     * @param string $path System Config Path
     *
     * @return mixed
     */
    public function getConfigData($path)
    {
        return Mage::getStoreConfig("carriers/{$this->_code}/{$path}");
    }
}