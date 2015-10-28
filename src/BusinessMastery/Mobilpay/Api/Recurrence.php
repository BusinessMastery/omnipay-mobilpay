<?php

namespace Omnipay\MobilPay\Api;

use DOMDocument;
use DOMNode;

class Recurrence
{
    const ERROR_INVALID_PARAMETER           = 0x11110001;
    const ERROR_INVALID_INTERVAL_DAY        = 0x11110002;
    const ERROR_INVALID_PAYMENTS_NO         = 0x11110003;

    const ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING = 0x31110001;

    public $interval_day = null;
    public $payments_no = null;

    public function __construct(DOMNode $elem = null)
    {
        if ($elem != null) {
            $this->loadFromXml($elem);
        }
    }

    protected function loadFromXml(DOMNode $elem)
    {
        $attr = $elem->attributes->getNamedItem('interval_day');

        if (is_null($attr)) {
            throw new Exception('Mobilpay_Payment_Recurrence::loadFromXml failed; interval_day attribute missing', self::ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING);
        }
        $this->interval_day = $attr->value;

        $attr = $elem->attributes->getNamedItem('payments_no');
        if (is_null($attr)) {
            throw new Exception('Mobilpay_Payment_Recurrence::loadFromXml failed; payments_no attribute missing', self::ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING);
        }
        $this->payments_no = $attr->value;
    }


    public function createXmlElement(DOMDocument $xmlDoc)
    {
        if (!($xmlDoc instanceof DOMDocument)) {
            throw new Exception('', self::ERROR_INVALID_PARAMETER);
        }

        $xmlInvElem = $xmlDoc->createElement('recurrence');

        if (is_null($this->interval_day)) {
            throw new Exception('Invalid interval_day', self::ERROR_INVALID_INTERVAL_DAY);
        }


        if (is_null($this->payments_no)) {
            throw new Exception('Invalid payments_no', self::ERROR_INVALID_PAYMENTS_NO);
        }

        $xmlAttr            = $xmlDoc->createAttribute('payments_no');
        $xmlAttr->nodeValue = $this->payments_no;
        $xmlInvElem->appendChild($xmlAttr);

        $xmlAttr            = $xmlDoc->createAttribute('interval_day');
        $xmlAttr->nodeValue = $this->interval_day;

        $xmlInvElem->appendChild($xmlAttr);

        return $xmlInvElem;
    }
}
