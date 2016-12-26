<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license http://opensource.org/licenses/MIT MIT
 */

class Freterapido_Freterapido_Model_Observer
{
    public function quotation($observer) {
        $data = $observer->getEvent()->getOrder();
        Mage::log($data->debug(), null, 'order.log', true);
    }
}