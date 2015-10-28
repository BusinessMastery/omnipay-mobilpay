<?php

namespace Omnipay\MobilPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * MobilPay Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @param  Omnipay\Common\Message\RequestInterface $request
     * @param  array $data
     * @param  string $redirectUrl
     * @return void
     */
    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {
        parent::__construct($request, $data);

        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Returns whether the transaction was successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Returns whether the transaction should continue
     * on a redirected page
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * Returns the redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Returns redirect URL method
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Returns the FORM data for the redirect
     *
     * @return array
     */
    public function getRedirectData()
    {
        return $this->getData();
    }
}
