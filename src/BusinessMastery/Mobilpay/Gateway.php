<?php namespace Omnipay\MobilPay;

use Omnipay\MobilPay\Message\CompletePurchaseRequest;
use Omnipay\MobilPay\Message\PurchaseRequest;
use Omnipay\Common\AbstractGateway;

/**
 * MobilPay Gateway
 *
 * @link http://www.mobilpay.ro
 */
class Gateway extends AbstractGateway {

    /**
     * @return string
     */
    public function getName()
    {
        return 'MobilPay';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'merchantId' => null,
            'publicKey'  => null,
            'testMode'   => false
        ];
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setPublicKey($value)
    {
        return $this->setParameter('publicKey', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setConfirmUrl($value)
    {
        return $this->setParameter('confirmUrl', $value);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\MobilPay\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\MobilPay\Message\CompletePurchaseRequest', $parameters);
    }
}
