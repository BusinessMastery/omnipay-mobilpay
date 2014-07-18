<?php

namespace Omnipay\MobilPay\Message;

use DOMDocument;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * MobilPay Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data)
    {
        echo '<pre>'; var_dump($data); exit;
        $this->request = $request;
        $this->data = new \SimpleXMLElement($data);
        if (!isset($this->data->status)) {
            throw new InvalidResponseException;
        }
    }

    public function isSuccessful()
    {
        return 1 === (int) $this->data->status;
    }

    public function isRedirect()
    {
        // 150 is the 3D Secure status code
        return 150 === (int) $this->data->status;
    }

    public function getTransactionReference()
    {
        return (string) $this->data->MobilPay_reference;
    }

    public function getTransactionId()
    {
        return (string) $this->data->merchantreference;
    }

    public function getMessage()
    {
        return (string) $this->data->reason;
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return (string) $this->data->CardTxn->ThreeDSecure->acs_url;
        }

        return '';
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $redirectData = array(
            'PaReq' => (string) $this->data->CardTxn->ThreeDSecure->pareq_message,
            'TermUrl' => $this->getRequest()->getReturnUrl(),
            'MD' => (string) $this->getTransactionId(),
        );
    }
}
