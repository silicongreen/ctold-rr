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

class Sslcommerz_NotificationController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        if (!$this->getRequest()->isPost()) {
            return;
            $insMessage = $this->getRequest()->getPost();
        }
    }


    public function insAction() {
        $insMessage = $this->getRequest()->getPost();
        foreach ($_REQUEST as $k => $v) {
        $v = htmlspecialchars($v);
        $v = stripslashes($v);
        $insMessage[$k] = $v;
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($insMessage['vendor_order_id']);
        $invoice_on_fraud = Mage::getStoreConfig('payment/invoice_on_fraud');
        $invoice_on_order = Mage::getStoreConfig('payment/invoice_on_order');
       	$hashSecretWord = Mage::getStoreConfig('payment/secret_word');
       	$hashSid = $insMessage['vendor_id'];
        $hashOrder = $insMessage['sale_id'];
        $hashInvoice = $insMessage['invoice_id'];
        $StringToHash = strtoupper(md5($hashOrder . $hashSid . $hashInvoice . $hashSecretWord));

        if ($StringToHash != $insMessage['md5_hash']) {
            die('Hash Incorrect');
        }

        if ($insMessage['message_type'] == 'FRAUD_STATUS_CHANGED') {
            if ($insMessage['fraud_status'] == 'fail') {
            	$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->addStatusHistoryComment('Order failed fraud review.')->save();

            } else if ($insMessage['fraud_status'] == 'pass') {
            	$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->addStatusHistoryComment('Order passed fraud review.')->save();
//Invoice
                if ($invoice_on_fraud == '1') {
                    try {
                        if(!$order->canInvoice())
                            {
                                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
                            }

                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                    if (!$invoice->getTotalQty()) {
                        Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
                    }

                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());

                    $transactionSave->save();
                    }
                    catch (Mage_Core_Exception $e) {
                    }
                }

            } else if ($insMessage['fraud_status'] == 'wait') {
                $order->addStatusHistoryComment('Order undergoing additional fraud investigation.');
                $order->save();
            }
        }

        if ($insMessage['message_type'] == 'ORDER_CREATED') {
//Invoice
            if ($invoice_on_order == '1') {
                try {
                    if(!$order->canInvoice())
                        {
                            Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
                        }

                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                    if (!$invoice->getTotalQty()) {
                        Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
                    }

                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());

                    $transactionSave->save();
                    }
                    catch (Mage_Core_Exception $e) {
                    }
                }
        }
    }
}

?>
