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

class Magentix_RatingRequest_Model_Observer
{

    const XML_PATH_ENABLED = 'ratingrequest/send/enabled';
    const XML_PATH_DAYS = 'ratingrequest/send/days';

    /**
     * Send Review Mail
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Magentix_RatingRequest_Model_Observer
     */
    public function sendMail($schedule)
    {
        if (!Mage::getStoreConfig(self::XML_PATH_ENABLED)) return $this;

        $shipments = $this->_getShipments();

        foreach ($shipments as $s) {
            $order = $s->getOrder();

            $sendTo = array(
                array(
                    'email' => strtolower($order->getCustomerEmail()),
                    'name' => $order->getCustomerName()
                )
            );

            Mage::getModel('ratingrequest/rating')->sendMail($sendTo, $order, true);

        }

        return $this;

    }

    /**
     * Add new Action to Shipment Actions
     *
     * @param Varien_Event_Observer $observer
     */
    public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'sales_shipment') {
            $block->addItem('ratingrequest', array(
                'label' => Mage::helper('ratingrequest')->__('Send Rating Request'),
                'url' => Mage::app()->getStore()->getUrl('*/email/send'),
            ));
        }
    }

    /**
     * Retrieve Shipments Collection
     *
     * @return Mage_Sales_Model_Mysql4_Order_Shipment_Collection
     */
    public function _getShipments()
    {
        $day = $this->_getDay(Mage::getStoreConfig(self::XML_PATH_DAYS));

        return Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToFilter('created_at', array('from' => $day . ' 00:00:00', 'to' => $day . ' 23:59:59'))
            ->load();
    }

    /**
     * Retrieve date by a given number of days
     *
     * @param int $diff
     * @return string
     */
    public function _getDay($diff = 1)
    {
        return strftime("%Y-%m-%d", mktime(0, 0, 0, date('m'), date('d') - (int)$diff, date('y')));
    }

}