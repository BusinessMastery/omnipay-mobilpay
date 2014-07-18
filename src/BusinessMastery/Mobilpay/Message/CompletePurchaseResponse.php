<?php namespace Omnipay\MobilPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * MobilPay Complete Purchase PDT Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    protected $status;
    protected $responseError;
    protected $action;

    public function __construct(RequestInterface $request, $data, $responseError)
    {
        parent::__construct($request, $data);

        $this->request       = $request;
        $this->responseError = $responseError;

        if (isset($data['objPmNotify']['action']))
        {
            $this->action = $data['objPmNotify']['action'];
        }
    }

    public function isSuccessful()
    {
        return in_array($this->action, ['confirmed']);
    }

    public function isPending()
    {
        return in_array($this->action, ['confirmed_pending', 'paid_pending', 'paid']);
    }

    public function getMessage()
    {
        return $this->action;
    }

    public function getData()
    {
        return $this->data;
    }

    public function sendResponse()
    {
        header('Content-type: application/xml');
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        if($this->responseError->code == 0)
        {
            echo "<crc>{$this->responseError->message}</crc>";
        }
        else
        {
            echo "<crc error_type=\"{$this->responseError->type}\" error_code=\"{$this->responseError->code}\">{$this->responseError->message}</crc>";
        }
    }
}
