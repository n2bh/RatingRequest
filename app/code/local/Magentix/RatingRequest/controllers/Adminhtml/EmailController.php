<?php
/**
 * Created by Magentix
 * Date: 31/08/12
 *
 * @category   Magentix
 * @package    Magentix_RatingRequest
 * @author     Matthieu Vion (http://www.magentix.fr)
 * @license    This work is free software, you can redistribute it and/or modify it
 */

class Magentix_RatingRequest_Adminhtml_EmailController extends Mage_Adminhtml_Controller_Action
{

    const XML_PATH_EMAIL_RECIPIENT = 'ratingrequest/test/recipient_email';

    /**
     * Send Test Action
     */
    public function testAction()
    {
        $shipment = Mage::getResourceModel('sales/order_shipment_collection')->load()->getFirstItem();

        if ($shipment->getId()) {
            $order = $shipment->getOrder();
            try {
                $sendTo = array(
                    array(
                        'email' => Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        'name' => $order->getCustomerName()
                    )
                );

                Mage::getModel('ratingrequest/rating')->sendMail($sendTo, $order);

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('A Mail was sent to %s', Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to send test : %s', $e->getMessage()));
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to send test : no shipment was found'));
        }

        $this->_redirect('*/system_config/edit', array('section' => 'ratingrequest'));

    }

    /**
     * Send Mass Action
     */
    public function sendAction()
    {
        $shipmentIds = $this->getRequest()->getPost('shipment_ids', array());

        if (!empty($shipmentIds)) {
            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();

            foreach ($shipments as $shipment) {
                $order = $shipment->getOrder();

                $sendTo = array(
                    array(
                        'email' => strtolower($order->getCustomerEmail()),
                        'name' => $order->getCustomerName()
                    )
                );

                Mage::getModel('ratingrequest/rating')->sendMail($sendTo, $order, true);
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s Request(s) was/were sent', count($shipmentIds)));
        }

        $this->_redirect('*/sales_shipment/index');
    }

}