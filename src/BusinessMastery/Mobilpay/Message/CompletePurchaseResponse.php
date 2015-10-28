<?php

namespace Omnipay\MobilPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * MobilPay Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * @var string
     */
    protected $action;

    /**
     * @var stdClass
     */
    protected $responseError;

    /**
     * @var string
     */
    protected $status;

    /**
     * @param  Omnipay\Common\Message\RequestInterface $request
     * @param  array $data
     * @param  stdClass $responseError
     * @return void
     */
    public function __construct(RequestInterface $request, $data, $responseError)
    {
        parent::__construct($request, $data);

        $this->request       = $request;
        $this->responseError = $responseError;

        if (isset($data['objPmNotify']['action'])) {
            $this->action = $data['objPmNotify']['action'];
        }
    }

    /**
     * Returns whether the transaction was successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return in_array($this->action, ['confirmed']);
    }

    /**
     * Returns whether the transaction is pending
     *
     * @return boolean
     */
    public function isPending()
    {
        return in_array($this->action, ['confirmed_pending', 'paid_pending', 'paid']);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Send IPN response
     *
     * @return void
     */
    public function sendResponse()
    {
        header('Content-type: application/xml');
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";

        if ($this->responseError->code == 0) {
            echo "<crc>{$this->responseError->message}</crc>";
        } else {
            echo "<crc error_type=\"{$this->responseError->type}\" error_code=\"{$this->responseError->code}\">{$this->responseError->message}</crc>";
        }
    }
}
