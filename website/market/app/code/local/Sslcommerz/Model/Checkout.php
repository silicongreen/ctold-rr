<?php

/*
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   SSLCOMMERZ Bangladesh Payment Gateway
 * @package    SSLCOMMERZ (https://sslcommerz.com.bd/)
 * @copyright  Copyright (c) 2012 Huffas Abdullah
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Sslcommerz_Model_Checkout extends Mage_Payment_Model_Method_Abstract {

    protected $_code  = 'ssl';
    protected $_formBlockType = 'ssl/form';
    protected $_paymentMethod = 'shared';

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('ssl/redirect');
    }

    //get SID
    public function getSid() {
        $sid = $this->getConfigData('sid');
        return $sid;
    }
    
    //get Exchange Rate
    public function getExchangeRate() {
        $exrate = $this->getConfigData('dollarrate');
        return $exrate;
    }

    //get Demo Setting
    public function getDemo() {
        if ($this->getConfigData('demo') == '1') 
        {
            $demo = 'Y';
    	} 
        else 
        {
            $demo = 'N';
    	}
	return $demo;
    }

    //get purchase routine URL
    public function getUrl() {
        if ($this->getDemo() == 'Y') 
        {
            $url = "https://www.sslcommerz.com.bd/testbox/process/index.php";
    	} 
        else 
        {
            $url = "https://www.sslcommerz.com.bd/process/index.php";
    	}
        return $url;
    }

    //get order
    public function getQuote() {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        return $order;
    }

    //get product data
    public function getProductData() 
    {
        $products = array();
        $items = $this->getQuote()->getAllItems();
        if ($items) {
            $i = 1;
            foreach($items as $item)
            {
                if ($item->getParentItem()) 
                {
                    continue;
                }
                $products['c_name_'.$i] = $item->getName();
                $products['c_description_'.$i] = $item->getSku();
                $products['c_price_'.$i] = number_format($item->getPrice() * $this->getExchangeRate(), 2, '.', '');
                $products['c_prod_'.$i] = $item->getSku() . ',' . $item->getQtyToInvoice();
                $i++;
            }
        }
        return $products;
    }

    //get lineitem data
    public function getLineitemData() 
    {
        $lineitems = array();
        $items = $this->getQuote()->getAllItems();
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $taxFull = $order->getFullTaxInfo();
        $ship_method   = $order->getShipping_description();
        $coupon = $order->getCoupon_code();
        $i = 1;
        //get products
        if ($items) 
        {
            foreach($items as $item)
            {
                if ($item->getParentItem()) {
                    continue;
                }
                $lineitems['li_'.$i.'_type'] = 'product';
                $lineitems['li_'.$i.'_product_id'] = $item->getSku();
                $lineitems['li_'.$i.'_quantity'] = $item->getQtyToInvoice();
                $lineitems['li_'.$i.'_name'] = $item->getName();
                $lineitems['li_'.$i.'_description'] = $item->getDescription();
                $lineitems['li_'.$i.'_price'] = number_format($item->getPrice() * $this->getExchangeRate(), 2, '.', '');
                $i++;
            }
        }
        
        //get taxes
        if ($taxFull) 
        {
            foreach($taxFull as $rate)
            {
                $lineitems['li_'.$i.'_type'] = 'tax';
                $lineitems['li_'.$i.'_name'] = $rate['rates']['0'][code];
                $lineitems['li_'.$i.'_price'] = round($rate['amount'], 2);
                $i++;
            }
        }
        
        if ($ship_method) 
        {
            $lineitems['li_'.$i.'_type'] = 'shipping';
            $lineitems['li_'.$i.'_name'] = $order->getShipping_description();
            $lineitems['li_'.$i.'_price'] = round($order->getShippingAmount(), 2);
            $i++;
        }
        
        //get coupons
        if ($coupon) 
        {
            $lineitems['li_'.$i.'_type'] = 'coupon';
            $lineitems['li_'.$i.'_name'] = $order->getCoupon_code();
            $lineitems['li_'.$i.'_price'] = trim(round($order->getBase_discount_amount(), 2), '-');
            $i++;
        }
        return $lineitems;
    }

    //get tax data
    public function getTaxData() {
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $taxes = array();
        $taxFull = $order->getFullTaxInfo();
        if ($taxFull) 
        {
            $i = 1;
            foreach($taxFull as $rate)
            {
                $taxes['tax_id_'.$i] = $rate['rates']['0'][code];
                $taxes['tax_amount_'.$i] = round($rate['amount'], 2);
                $i++;
            }
        }
        return $taxes;
    }

    //get HTML form data
    public function getFormFields() 
    {
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $amount   = round($order->getGrandTotal(), 2);
        $a = $this->getQuote()->getShippingAddress();
        $b = $this->getQuote()->getBillingAddress();
        $country = $b->getCountry();
        $currency_code = $this->getQuote()->getCurrencyCode();
        $shipping = round($order->getShippingAmount(), 2);
        $weight = round($order->getWeight(), 2);
        $ship_method   = $order->getShipping_description();
        $tax = trim(round($order->getTaxAmount(), 2));
        $productData = $this->getProductData();
        $taxData = $this->getTaxData();
        $cart_order_id = $order_id;
        $lineitemData = $this->getLineitemData();

        $sslFields = array();
        $sslFields['store_id']          = $this->getSid();
        $sslFields['tran_id']           = $order_id;
        $sslFields['email']             = $order->getData('customer_email');
        $sslFields['first_name']        = $b->getFirstname();
        $sslFields['last_name']         = $b->getLastname();
        $sslFields['phone']             = $b->getTelephone();
        $sslFields['country']           = $b->getCountry();
        $sslFields['street_address']    = $b->getStreet1();
        $sslFields['street_address2']   = $b->getStreet2();
        $sslFields['city']              = $b->getCity();

        $sslFields['zip']               = $b->getPostcode();

        if ($a) 
        {
            $sslFields['ship_name']             = $a->getFirstname() . ' ' . $a->getLastname();
            $sslFields['ship_country']          = $a->getCountry();
            $sslFields['ship_street_address']   = $a->getStreet1();
            $sslFields['ship_street_address2']  = $a->getStreet2();
            $sslFields['ship_city']             = $a->getCity();
            $sslFields['ship_state']            = $a->getRegion();
            $sslFields['ship_zip']              = $a->getPostcode();
        } 
        else 
        {
            $sslFields['ship_name']             = $b->getFirstname() . ' ' . $b->getLastname();
            $sslFields['ship_country']          = $b->getCountry();
            $sslFields['ship_street_address']   = $b->getStreet1();
            $sslFields['ship_street_address2']  = $b->getStreet2();
            $sslFields['ship_city']             = $b->getCity();
            $sslFields['ship_state']            = $b->getRegion();
            $sslFields['ship_zip']              = $b->getPostcode();
        }
        
        $sslFields['sh_cost']                   = $shipping;
        $sslFields['sh_weight']                 = $weight;
        $sslFields['ship_method']               = $ship_method;
        $sslFields['success_url']               = Mage::getUrl('ssl/redirect/success', array('_secure' => true));
        $sslFields['cancel_url']                = Mage::getUrl('ssl/redirect/cancel', array('_secure' => true));
        $sslFields['fail_url']                  = Mage::getUrl('ssl/redirect/fail', array('_secure' => true));

        $sslFields['total_amount']              = number_format($amount * $this->getExchangeRate(), 2, '.', '');
        $result = $sslFields + $lineitemData;
        return $result;
    }

}
