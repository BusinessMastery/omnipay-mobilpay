<?php

namespace Omnipay\MobilPay\Api;

/**
 * Class Invoice
 * @copyright NETOPIA System
 * @author Claudiu Tudose
 * @version 1.0
 */

use DOMDocument;
use DOMNode;

class Invoice
{
    const ERROR_INVALID_PARAMETER            = 0x11110001;
    const ERROR_INVALID_CURRENCY            = 0x11110002;
    const ERROR_ITEM_INSERT_INVALID_INDEX    = 0x11110003;

    const ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING    = 0x31110001;

    public $currency                = null;
    public $amount                  = null;
    public $details                 = null;
    public $installments            = null;
    public $selectedInstallments    = null;


    protected $billingAddress    = null;
    protected $shippingAddress   = null;

    protected $items            = [];
    protected $exchangeRates    = [];

    public function __construct(DOMNode $elem = null)
    {
        if ($elem != null) {
            $this->loadFromXml($elem);
        }
    }

    protected function loadFromXml(DOMNode $elem)
    {
        $attr = $elem->attributes->getNamedItem('currency');
        if ($attr == null) {
            throw new Exception('Invoice::loadFromXml failed; currency attribute missing', self::ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING);
        }
        $this->currency = $attr->nodeValue;

        $attr = $elem->attributes->getNamedItem('amount');
        if ($attr != null) {
            $this->amount = $attr->nodeValue;
        }

        $attr = $elem->attributes->getNamedItem('installments');
        if ($attr != null) {
            $this->installments = $attr->nodeValue;
        }

        $attr = $elem->attributes->getNamedItem('selected_installments');
        if ($attr != null) {
            $this->selectedInstallments = $attr->nodeValue;
        }

        $elems = $elem->getElementsByTagName('details');
        if ($elems->length == 1) {
            $this->details = urldecode($elems->item(0)->nodeValue);
        }

        $elems = $elem->getElementsByTagName('contact_info');
        if ($elems->length == 1) {
            $addrElem = $elems->item(0);

            $elems = $addrElem->getElementsByTagName('billing');
            if ($elems->length == 1) {
                $this->billingAddress = new Address($elems->item(0));
            }

            $elems = $addrElem->getElementsByTagName('shipping');
            if ($elems->length == 1) {
                $this->shippingAddress = new Address($elems->item(0));
            }
        }
    }

    public function createXmlElement(DOMDocument $xmlDoc)
    {
        if (!($xmlDoc instanceof DOMDocument)) {
            throw new Exception('', self::ERROR_INVALID_PARAMETER);
        }

        $xmlInvElem = $xmlDoc->createElement('invoice');

        if ($this->currency == null) {
            throw new Exception('Invalid currency', self::ERROR_INVALID_CURRENCY);
        }

        $xmlAttr            = $xmlDoc->createAttribute('currency');
        $xmlAttr->nodeValue    = $this->currency;
        $xmlInvElem->appendChild($xmlAttr);

        if ($this->amount != null) {
            $xmlAttr            = $xmlDoc->createAttribute('amount');
            $xmlAttr->nodeValue = sprintf('%.02f', doubleval($this->amount));
            $xmlInvElem->appendChild($xmlAttr);
        }

        if ($this->installments != null) {
            $xmlAttr            = $xmlDoc->createAttribute('installments');
            $xmlAttr->nodeValue = $this->installments;
            $xmlInvElem->appendChild($xmlAttr);
        }

        if ($this->selectedInstallments != null) {
            $xmlAttr            = $xmlDoc->createAttribute('selected_installments');
            $xmlAttr->nodeValue = $this->selectedInstallments;
            $xmlInvElem->appendChild($xmlAttr);
        }

        if ($this->details != null) {
            $xmlElem            = $xmlDoc->createElement('details');
            $xmlElem->appendChild($xmlDoc->createCDATASection(urlencode($this->details)));
            $xmlInvElem->appendChild($xmlElem);
        }

        if (($this->billingAddress instanceof Address) || ($this->shippingAddress instanceof Address)) {
            $xmlAddr = null;
            if ($this->billingAddress instanceof Address) {
                try {
                    $xmlElem = $this->billingAddress->createXmlElement($xmlDoc, 'billing');
                    if ($xmlAddr == null) {
                        $xmlAddr = $xmlDoc->createElement('contact_info');
                    }
                    $xmlAddr->appendChild($xmlElem);
                } catch (Exception $e) {
                    $e = $e;
                }
            }
            if ($this->shippingAddress instanceof Address) {
                try {
                    $xmlElem = $this->shippingAddress->createXmlElement($xmlDoc, 'shipping');
                    if ($xmlAddr == null) {
                        $xmlAddr = $xmlDoc->createElement('contact_info');
                    }
                    $xmlAddr->appendChild($xmlElem);
                } catch (Exception $e) {
                    $e = $e;
                }
            }
            if ($xmlAddr != null) {
                $xmlInvElem->appendChild($xmlAddr);
            }
        }

        return $xmlInvElem;
    }

    public function setBillingAddress(Address $address)
    {
        $this->billingAddress = $address;

        return $this;
    }

    public function setShippingAddress(Address $address)
    {
        $this->shippingAddress = $address;

        return $this;
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
}
