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

// Fix issue with include path
if (!class_exists('Mage_Catalog_ProductController', false)) {
    require_once Mage::getModuleDir('controllers', 'Mage_Catalog') . DS . 'ProductController.php';
}

/**
 * Estimate shiping controller, passes the request to estimate model
 * Extended from product controller for supporting of full product initialization
 *
 */
class Freterapido_ProductPageShipping_EstimateController extends Mage_Catalog_ProductController
{
    /**
     * Estimate action
     *
     * Initializes the product and passes data to estimate model in block
     */
    public function estimateAction()
    {
        $product = $this->_initProduct();
        $this->loadLayout(false);
        $block = $this->getLayout()->getBlock('shipping.estimate.result');
        if ($block) {
            $estimate = $block->getEstimate();
            $product->setAddToCartInfo((array) $this->getRequest()->getPost());
            $estimate->setProduct($product);
            $addressInfo = $this->getRequest()->getPost('estimate');
            $estimate->setAddressInfo((array) $addressInfo);
            $block->getSession()->setFormValues($addressInfo);
            try {
                $estimate->estimate();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('catalog/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('catalog/session')->addError(
                    Mage::helper('freterapido_productpageshipping')->__('Ops, ocorreu algum erro durente a cotação do frete com as transportadoras')
                );
            }
        }
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
}
