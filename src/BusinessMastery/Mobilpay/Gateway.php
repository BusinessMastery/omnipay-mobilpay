<?php

namespace Omnipay\MobilPay;

use Omnipay\MobilPay\Message\CompletePurchaseRequest;
use Omnipay\MobilPay\Message\PurchaseRequest;
use Omnipay\Common\AbstractGateway;

/**
 * MobilPay Gateway
 *
 * @link http://www.mobilpay.ro
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'MobilPay';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => null,
            'publicKey'  => null,
            'testMode'   => false
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function publicKeyPath($value)
    {
        return $this->setParameter('publicKey', $value);
    }

    public function privateKeyPath($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MobilPay\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\MobilPay\Message\CompletePurchaseRequest', $parameters);
    }
}
