<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license http://opensource.org/licenses/MIT MIT
 */

class Freterapido_Freterapido_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_sender = array(
        'cnpj' => null,
        'inscricao_estadual' => null,
        'endereco' => array(
            'cep' => null
        )
    );

    protected $_receiver = array(
        'tipo_pessoa' => 1,
        'endereco' => array(
            'cep' => null
        )
    );

    protected $_carrier = array(
        'transportadora' => null,
        'valor' => 0
    );

    protected $_url = null;

    protected $_offer = array();

    protected $_success = true;

    public function quote($observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        $order = $shipment->getOrder();

        $customer = Mage::getModel('customer/customer')->load($shipment->getCustomerId());

        try {
            $this->_log('Iniciando contratação...');

            $this->_getSender();

            $this->_getReceiver($order, $customer);

            $this->_getOffer($order->getShippingMethod());

            $this->_url = sprintf(Mage::getStoreConfig('carriers/freterapido/api_quote_url'),
                $this->_offer['token'], $this->_offer['code'], Mage::getStoreConfig('carriers/freterapido/token')
            );

            $this->_doQuote();

            $this->_log('Contratação realizada com sucesso.');

            return $this;
        } catch (Exception $e) {
            $this->_throwError($e->getMessage());
        }
    }

    /**
     * Obtém os dados da origem
     */
    protected function _getSender()
    {
        try {
            Mage::getStoreConfig('shipping/origin', $this->getStore());

            $this->_sender = array();
            $this->_sender['cnpj'] = Mage::getStoreConfig('carriers/freterapido/shipper_cnpj');

        } catch (Exception $e) {
            $this->_throwError('Erro ao tentar obter os dados de origem. Erro: ' . $e->getMessage());
        }
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    protected function _getReceiver($order, $customer)
    {
        try {
            $name = $customer->getFirstname() .
                ' ' .
                $customer->getMiddlename() .
                ' ' .
                $customer->getLastname();

            $this->_receiver = array();
//            $this->_receiver['tipo_pessoa'] = 1;
            $this->_receiver['cnpj_cpf'] = preg_replace("/\D/", '', $customer->getTaxvat());
            $this->_receiver['nome'] = $name;
            $this->_receiver['email'] = $order->getShippingAddress()->getData('email');
            $this->_receiver['telefone'] = preg_replace("/\D/", '', $order->getShippingAddress()->getData('telephone'));

            $this->_receiver['endereco']['cep'] = $this->_formatZipCode($order->getShippingAddress()->getData('postcode'));
            $this->_receiver['endereco']['rua'] = $order->getShippingAddress()->getData('street');

        } catch (Exception $e) {
            $this->_throwError('Erro ao tentar obter os dados de origem. Erro: ' . $e->getMessage());
        }
    }

    /**
     * Formata e valida o CEP informado
     *
     * @param string $zipcode
     * @return boolean|string
     */
    protected function _formatZipCode($zipcode)
    {
        $new_zipcode = preg_replace("/\D/", '', trim($zipcode));

        if (strlen($new_zipcode) !== 8) {
           throw new Exception('O CEP digitado é inválido');
        }

        return $new_zipcode;
    }

    protected function _getOffer($shipping_method)
    {
        $method = explode('_', $shipping_method);
        $last_index = count($method) - 1;

        $this->_offer = [
            'token' => $method[$last_index - 1],
            'code' => $method[$last_index]
        ];

    }

    protected function _doQuote()
    {
        // Dados que serão enviados para a API do Frete Rápido
        $request_data = array(
            'remetente' => $this->_sender,
            'destinatario' => $this->_receiver,
        );

        $this->_log($this->_url . ' | ' . json_encode($request_data));

        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER, false
            ),
        );

        // Configura o cliente http passando a URL da API e a configuração
        $client = new Zend_Http_Client($this->_url, $config);

        // Adiciona os parâmetros à requisição
        $client->setRawData(json_encode($request_data), 'application/json');

        // Realiza a chamada POST
        $response = $client->request('POST');

        if ($response->getStatus() != 200) {
            throw new Exception('Erro ao tentar se comunicar com a API - Code: ' . $response->getStatus() . '. Error: ' . $response->getMessage());
        }
    }

    /**
     * Armazena no log a mensagem informada
     *
     * @param string $mensagem
     */
    protected function _log($mensagem)
    {
        Mage::log('Frete Rápido: ' . $mensagem);
    }


    /**
     * Armazena no log a mensagem informada
     *
     * @param string $mensagem
     */
    protected function _throwError($mensagem)
    {
        Mage::throwException('Frete Rápido - Não foi possível realizar a contratação do frete. Motivo: ' . $mensagem);
    }
}