<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 */

class Freterapido_Freterapido_Model_Tracking
{
    const CODE = 'freterapido';

    const TITLE = 'Frete Rápido';

    protected $_code = self::CODE;

    protected $_title = self::TITLE;

    protected $_result = null;

    protected $_tracking_number = null;

    /**
     * Consulta o tracking
     *
     * @param array $trackings Trackings
     * @return Mage_Shipping_Model_Tracking_Result
     */
    public function getTracking($trackings)
    {
        $this->_result = Mage::getModel('shipping/tracking_result');

        foreach ((array) $trackings as $code) {
            $this->_getFrTracking($code);
        }

        return $this->_result;
    }

    /**
     * Consulta e retornar o tracking
     *
     * @param array $tracking
     * @return void
     */
    protected function _getFrTracking($tracking_number)
    {
        $this->_tracking_number = $tracking_number;

        // Seta o erro para armazenar caso seja necessário
        $error = $this->_setTrackingError();

        try {
            $response = $this->_requestApi();

            if (200 != $response->getStatus()) {
                throw new Exception(
                    'Erro ao tentar consultar o tracking do pedido ' . $this->_tracking_number .
                    '. ResponseStatus: ' . $response->getStatus()
                );
            }

            // Lista dos status do frete
            $occurrences = json_decode($response->getBody());

            // Obtém os status do frete no padrão do MAgento
            $progress = $this->_getTrackingProgress($occurrences);

            if (!empty($progress)) {
                // Seta os status do tracking para retornar no front
                $tracking = $this->_setTrackingProgress($progress);
                $this->_result->append($tracking);

            } else {
                throw new Exception('Nenhuma ocorrência foi encontrada para o frete ' . $this->_tracking_number);
            }

        } catch (Exception $e) {
            $this->_log($e->getMessage());
            $this->_result->append($error);
        }
    }

    /**
     * Realiza a requisição para a API de tracking do Frete Rápido
     *
     * @param $tracking_number
     * @return Zend_Http_Response
     */
    protected function _requestApi()
    {
        // Configura a url com o id do frete e o token de acesso
        $api_url = sprintf(
            Mage::helper($this->_code)->getConfigData('api_tracking_url'),
            preg_replace("/\W/", '', $this->_tracking_number),
            Mage::helper($this->_code)->getConfigData('token')
        );

        // Configura a chamada com a API
        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER, false
            ),
        );

        // Configura o cliente http passando a URL da API e a configuração
        $client = new Zend_Http_Client($api_url, $config);

        // Realiza a chamada GET
        return $client->request('GET');
    }

    /**
     * Obtém os status do tracking no formato do Magento
     *
     * @param $occurrences
     * @return array|bool
     */
    protected function _getTrackingProgress($occurrences)
    {
        $progress = array();

        if (empty($occurrences))
            return false;

        foreach ($occurrences as $occurrence) {
            $date = new DateTime($occurrence->data_ocorrencia);

            $progress[] = array(
                'deliverydate' => $date->format('Y-m-d'),
                'deliverytime' => $date->format('H:i'),
                'activity' => $occurrence->nome,
                'status' => $occurrence->nome,
            );
        }

        return $progress;
    }

    /**
     * Seta as informações do tracking para exibir
     *
     * @param $progress array
     * @param $tracking_number string
     * @return false|Mage_Core_Model_Abstract
     */
    protected function _setTrackingProgress($progress)
    {
        $track = array_pop($progress);
        $track['progressdetail'] = $progress;

        $resource = Mage::getResourceModel('sales/order_shipment_track_collection')
            ->addAttributeToSelect('title')
            ->addAttributeToFilter('track_number', $this->_tracking_number)
            ->load();

        $resource->getSelect()->limit(1);
        $resource = $resource->getData();

        $carrier = empty($resource[0]['title']) ? $this->_title : $resource[0]['title'];

        $tracking = Mage::getModel('shipping/tracking_result_status');
        $tracking->setTracking($this->_tracking_number);
        $tracking->setCarrier($this->_code);
        $tracking->setCarrierTitle($carrier);
        $tracking->addData($track);

        return $tracking;
    }

    /**
     * @param $tracking_number
     * @return false|Mage_Core_Model_Abstract
     */
    protected function _setTrackingError()
    {
        $error = Mage::getModel('shipping/tracking_result_error');
        $error->setTracking($this->_tracking_number);
        $error->setCarrier($this->_code);
        $error->setCarrierTitle($this->_title);
        $error->setErrorMessage('Erro ao tentar consultar o tracking do pedido ' . $this->_tracking_number);

        return $error;
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
}