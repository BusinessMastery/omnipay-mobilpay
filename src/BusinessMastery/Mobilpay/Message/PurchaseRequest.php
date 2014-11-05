<?php namespace Omnipay\MobilPay\Message;

use DOMDocument;
use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\MobilPay\Exception\MissingKeyException;
use Omnipay\MobilPay\Api\Request\Card;
use Omnipay\MobilPay\Api\Invoice;
use Omnipay\MobilPay\Api\Recurrence;
use Omnipay\MobilPay\Api\Address;

/**
 * MobilPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest {

    /**
     * @var string
     */
    protected $liveEndpoint = 'https://secure.mobilpay.ro';

    /**
     * @var string
     */
    protected $testEndpoint = 'http://sandboxsecure.mobilpay.ro';

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
     * @return string
     */
    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
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
     * @return string
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
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
     * @return string
     */
    public function getConfirmUrl()
    {
        return $this->getParameter('confirmUrl');
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
     * @return array
     */
    public function getParams()
    {
        return $this->getParameter('params');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setParams($value)
    {
        return $this->setParameter('params', $value);
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->getParameter('details');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setDetails($value)
    {
        return $this->setParameter('details', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function getRecurrence()
    {
        return $this->getParameter('recurrence');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setRecurrence($value)
    {
        return $this->setParameter('recurrence', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function getPaymentNo()
    {
        return $this->getParameter('paymentNo');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setPaymentNo($value)
    {
        return $this->setParameter('paymentNo', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function getIntervalDay()
    {
        return $this->getParameter('intervalDay');
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function setIntervalDay($value)
    {
        return $this->setParameter('intervalDay', $value);
    }

    /**
     * Build encrypted request data
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('amount', 'currency', 'orderId', 'confirmUrl', 'returnUrl', 'details');

        $envKey = $envData = null;
        $publicKey = $this->getParameter('publicKey');

        if ( ! $publicKey)
        {
            throw new MissingKeyException("Missing public key path parameter");
        }

        $request = new Card();
        $request->signature  = $this->getMerchantId();
        $request->orderId    = $this->getParameter('orderId');
        $request->confirmUrl = $this->getParameter('confirmUrl');
        $request->returnUrl  = $this->getParameter('returnUrl');
        $request->params     = $this->getParameter('params') ?: [];

        if ($this->getParameter('recurrence'))
        {
            $request->recurrence = new Recurrence();
            $request->recurrence->payments_no = $this->getParameter('paymentNo');;
            $request->recurrence->interval_day = $this->getParameter('intervalDay');;
        }

        $request->invoice = new Invoice();
        $request->invoice->currency = $this->getParameter('currency');
        $request->invoice->amount   = $this->getParameter('amount');
        $request->invoice->details  = $this->getParameter('details');
        
        if($this->getParameter('billingAddress')) {
        	$addressData =  $this->getParameter('billingAddress');
        	$billingAddress = new Address();
        	
        	foreach($addressData as $key => $value) {
        		$billingAddress->$key = $value;
        	}
        	
        	$request->invoice->setBillingAddress($billingAddress);
        	$request->invoice->setShippingAddress($billingAddress);
        }
        
        if(!$request->service) {
        	unset($request->service);
        } 

        $request->encrypt($this->getParameter('publicKey'));
        
        $data = [
            'env_key' => $request->getEnvKey(),
            'data'    => $request->getEncData()
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @param array $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data, $this->getEndpoint());
    }
    
    /**
     * @param unknown $address
     */
    public function setBillingAddress($address)
    {
    	return $this->setParameter('billingAddress', $address);
    }
    
    /**
     *
     */
    public function getBillingAddress()
    {
    	return $this->getParameter('billingAddress');
    }
}
