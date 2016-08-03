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

class Sslcommerz_RedirectController extends Mage_Core_Controller_Front_Action {

    public function getCheckout() {
    return Mage::getSingleton('checkout/session');
    }

    protected $order;

    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function indexAction() {
        $this->getResponse()
                ->setHeader('Content-type', 'text/html; charset=utf8')
                ->setBody($this->getLayout()
                ->createBlock('ssl/redirect')
                ->toHtml());
    }
    
    /**
     * Cancel Express Checkout
     */
    public function cancelAction()
    {
        try {
            $this->_initToken('cancel');
            // TODO verify if this logic of order cancelation is deprecated
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? Mage::getModel('sales/order')->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess($this->__('SSLCOMMERZ Payment and Order have been canceled.'))
                ;
            } else {
                $this->_getCheckoutSession()->addSuccess($this->__('SSLCOMMERZ Payment has been canceled.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to cancel SSLCOMMERZ Payment.'));
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }
    
    /**
     * Cancel Express Checkout
     */
    public function failAction()
    {
        try {
            $this->_initToken('fail');
            // TODO verify if this logic of order cancelation is deprecated
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? Mage::getModel('sales/order')->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess($this->__('Invalid SSLCOMMERZ Payment and Order have been canceled.'))
                ;
            } else {
                $this->_getCheckoutSession()->addSuccess($this->__('Invalid SSLCOMMERZ Payment, so order has been canceled.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to cancel SSLCOMMERZ Payment.'));
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }
    
    /**
     * Search for proper checkout token in request or session or (un)set specified one
     * Combined getter/setter
     *
     * @param string $setToken
     * @return Mage_Paypal_ExpressController|string
     */
    protected function _initToken($setToken = '')
    {
        if ('cancel' === $setToken) 
        {
            // security measure for avoid unsetting token twice
            Mage::throwException($this->__('SSLCOMMERZ Payment has been ' . $setToken . '.'));
        }
        else
        {
            Mage::throwException($this->__('An invalid payment request found.'));
        }
        return $this;
    }
    
    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function successAction() {
        $post = $this->getRequest()->getPost();
        $insMessage = $this->getRequest()->getPost();
        foreach ($_REQUEST as $k => $v) {
            $v = htmlspecialchars($v);
            $v = stripslashes($v);
            $post[$k] = $v;
        }

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($post['tran_id']);
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
       
        
        $val_id = $post['val_id'];

        try
        {
            $c = new soapclient('https://www.sslcommerz.com.bd/testbox/validator/validationserver.php?wsdl');
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        }

        $res = $c->checkValidation($val_id);  //  here $res will get

        if (strcmp ($res, "VALID") == 0) 
        {
            $this->_redirect('checkout/onepage/success');
            $order->sendNewOrderEmail();
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
            $order->setData('ext_order_id',$post['order_number'] );
            $order->save();
        }
        else  
        {
            $this->_redirect('ssl/redirect/fail');
            $order->addStatusHistoryComment('An invalid payment is found so order is not procced.');
            $order->save();
        }

        
    }

}

?>
