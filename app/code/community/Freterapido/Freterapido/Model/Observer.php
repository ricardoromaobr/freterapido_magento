<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 */
class Freterapido_Freterapido_Model_Observer extends Mage_Core_Model_Abstract
{
    const CODE = 'freterapido';

    const TITLE = 'Frete Rápido';

    protected $_code = self::CODE;

    protected $_title = self::TITLE;

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

    protected $_url = null;

    protected $_offer = array();

    protected $_increment_id = null;

    protected $_track_id = null;

    protected $_success = true;

    public function quote($observer)
    {
        $active = (boolean) Mage::helper($this->_code)->getConfigData('active');

        // Se o módulo não estiver ativo, ignora a contratação pelo Frete Rápido
        if (!$active) {
            return false;
        }

        $_shipment = $observer->getEvent()->getShipment();

        $_order = $_shipment->getOrder();

        // If order shipping method isn't ours, ignore it
        if (strpos($_order->getShippingMethod(), 'freterapido') === false) {
            return false;
        }

        try {
            $this->_increment_id = $_order->getIncrementId();

            // O magento connect não permite verificar direto no método
            $_customer_id = $_shipment->getCustomerId();
            $_vat_id = $_order->getShippingAddress()->getData('vat_id');

            // Verifica se o checkout foi feito com o usuário logado ou não, pois a forma de obter o cnpj/cpf é diferente em cada caso
            if (!empty($_customer_id)) {
                $_cnpj_cpf = Mage::getModel('customer/customer')->load($_customer_id)->getData('taxvat');
            } elseif (!empty($_vat_id)) {
                $_cnpj_cpf = $_vat_id;
            } else {
                $_cnpj_cpf = $_order->getData('customer_taxvat');
            }

            if (empty($_cnpj_cpf)) {
                throw new Exception('O CNPJ/CPF do destinatário não foi informado.');
            }

            $_cnpj_cpf = preg_replace("/\D/", '', $_cnpj_cpf);

            $this->_log('Iniciando contratação...');

            $this->_getSender();

            $this->_getReceiver($_order, $_cnpj_cpf);

            $this->_getOffer($_order->getShippingMethod());

            $this->_url = sprintf(
                Mage::helper($this->_code)->getConfigData('api_quote_url'),
                $this->_offer['token'],
                $this->_offer['code'],
                Mage::helper($this->_code)->getConfigData('token')
            );

            $this->_doHire();

            $this->_addTracking($_shipment);

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
     * @param  Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    protected function _getReceiver($order, $cnpj_cpf)
    {
        try {
            $name = $order->getShippingAddress()->getFirstname()
                . ' '
                . $order->getShippingAddress()->getLastname();

            $this->_receiver = array();
            $this->_receiver['cnpj_cpf'] = preg_replace("/\D/", '', $cnpj_cpf);
            $this->_receiver['nome'] = $name;
            $this->_receiver['email'] = $order->getShippingAddress()->getEmail();
            $this->_receiver['telefone'] = preg_replace("/\D/", '', $order->getShippingAddress()->getTelephone());

            $this->_receiver['endereco']['cep'] = $this->_formatZipCode($order->getShippingAddress()->getPostcode());
            $this->_receiver['endereco']['rua'] = $order->getShippingAddress()->getData('street');
        } catch (Exception $e) {
            $this->_throwError('Erro ao tentar obter os dados de origem. Erro: ' . $e->getMessage());
        }
    }

    /**
     * Formata e valida o CEP informado
     *
     * @param  string         $zipcode
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

    /**
     * Separa o token e o código da oferta armazenados no shipping method
     *
     * @param string $shipping_method
     */
    protected function _getOffer($shipping_method)
    {
        $method = explode('_', $shipping_method);
        $last_index = count($method) - 1;

        $this->_offer = array(
            'token' => $method[$last_index - 1],
            'code' => $method[$last_index]
        );
    }

    /**
     * Realiza a contratação do frete no Frete Rápido
     *
     * @throws Exception
     */
    protected function _doHire()
    {
        // Dados que serão enviados para a API do Frete Rápido
        $request_data = array(
            'numero_pedido' => $this->_increment_id,
            'remetente' => $this->_sender,
            'destinatario' => $this->_receiver,
        );

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
            $_message = $response->getStatus() == 422 ? $response->getBody() : $response->getMessage();

            throw new Exception('Erro ao tentar se comunicar com a API - Code: ' . $response->getStatus() . '. Error: ' . $_message);
        }

        $response = json_decode($response->getBody());

        $this->_track_id = $response->id_frete;
    }

    /**
     * Adiciona o id de acompanhamento do Frete Rápido no tracking da ordem
     *
     * @param $shipment
     */
    protected function _addTracking($shipment)
    {
        $_shipping_method = explode('-', $shipment->getOrder()->getShippingDescription());
        $shipping_method = trim($_shipping_method[0]);

        $carrier = empty($shipping_method) ? $this->_title : $shipping_method;

        $track = Mage::getModel('sales/order_shipment_track')
            ->setNumber($this->_track_id) //tracking number / awb number
            ->setCarrierCode($this->_code) //carrier code
            ->setTitle($carrier); //carrier title

        $shipment->addTrack($track);
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
