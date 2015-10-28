<?php

namespace Omnipay\MobilPay\Message;

use stdClass;
use Omnipay\MobilPay\Api\Request\AbstractRequest;
use Omnipay\MobilPay\Exception\MissingKeyException;

/**
 * MobilPay Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    /**
     * @var stdClass
     */
    private $responseError;

    /**
     * @param  string $value
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function getIpnData()
    {
        return $this->getParameter('ipn_data');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setData($value)
    {
        return $this->setParameter('ipn_data', $value);
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function getIpnEnvKey()
    {
        return $this->getParameter('ipn_env_key');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setEnvKey($value)
    {
        return $this->setParameter('ipn_env_key', $value);
    }

    /**
     * Process IPN request data
     *
     * @return array
     */
    public function getData()
    {
        if (! $this->getPrivateKey()) {
            throw new MissingKeyException("Missing private key path parameter");
        }

        $data = [];
        $this->responseError = new stdClass();

        $this->responseError->code    = 0;
        $this->responseError->type    = AbstractRequest::CONFIRM_ERROR_TYPE_NONE;
        $this->responseError->message = '';

        if ($this->getIpnEnvKey() && $this->getIpnData()) {
            try {
                $data = AbstractRequest::factoryFromEncrypted(
                    $this->getIpnEnvKey(),
                    $this->getIpnData(),
                    $this->getPrivateKey()
                );

                $this->responseError->message = $data->objPmNotify->getCrc();

                $data = json_decode(json_encode($data), true);

                // extract the transaction status from the IPN message
                if (isset($data['objPmNotify']['action'])) {
                    $this->action = $data['objPmNotify']['action'];
                }

                if (! in_array(
                    $this->action,
                    ['confirmed_pending', 'paid_pending', 'paid', 'confirmed', 'canceled', 'credit']
                )) {
                    $this->responseError->type    = AbstractRequest::CONFIRM_ERROR_TYPE_PERMANENT;
                    $this->responseError->code    = AbstractRequest::ERROR_CONFIRM_INVALID_ACTION;
                    $this->responseError->message = 'mobilpay_refference_action paramaters is invalid';
                }
            } catch (Exception $e) {
                $this->responseError->type    = AbstractRequest::CONFIRM_ERROR_TYPE_TEMPORARY;
                $this->responseError->code    = $e->getCode();
                $this->responseError->message = $e->getMessage();
            }
        } else {
            $this->responseError->type    = AbstractRequest::CONFIRM_ERROR_TYPE_PERMANENT;
            $this->responseError->code    = AbstractRequest::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
            $this->responseError->message = 'mobilpay.ro posted invalid parameters';
        }

        return $data;
    }

    /**
     * Build IPN response message
     *
     * @param  array $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data, $this->responseError);
    }
}
