<?php

/**
 * This source file is subject to the MIT License.
 * It is also available through http://opensource.org/licenses/MIT
 *
 * @category  Akhilleus
 * @package   LithiumSoftware_Akhilleus
 * @author    LithiumSoftware <contato@lithiumsoftware.com.br>
 * @copyright 2015 Lithium Software
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Freterapido_Freterapido_Model_Source_ResultOptions
{
    /**
     * Get options for weight
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('Sem filtro (todas as ofertas)')),
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Somente a oferta com menor preço')),
            array('value' => 2, 'label' => Mage::helper('adminhtml')->__('Somente a oferta com menor prazo de entrega'))
        );
    }
}
