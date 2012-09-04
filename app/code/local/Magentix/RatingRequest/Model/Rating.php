<?php
/**
 * Created by Magentix
 * Date: 03/09/12
 *
 * @category   Magentix
 * @package    Magentix_RatingRequest
 * @author     Matthieu Vion (http://www.magentix.fr)
 * @license    This work is free software, you can redistribute it and/or modify it
 */

class Magentix_RatingRequest_Model_Rating extends Mage_Core_Model_Abstract
{

    const XML_PATH_EMAIL_SENDER = 'ratingrequest/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE = 'ratingrequest/email/email_template';
    const XML_PATH_EMAIL_COPY_TO = 'ratingrequest/email/copy_to';

    /**
     * Send Rating Request
     *
     * @param Array $sendTo
     * @param Mage_Sales_Model_Order $order
     * @param bool $copy
     * @return Magentix_RatingRequest_Model_Send
     */
    public function sendMail($sendTo, $order, $copy = false)
    {
        if($copy) {
            $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);

            if ($copyTo) {
                foreach ($copyTo as $email) {
                    $sendTo[] = array(
                        'email' => $email,
                        'name' => null
                    );
                }
            }
        }

        $mailTemplate = Mage::getModel('core/email_template');

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                $recipient['email'],
                $recipient['name'],
                array(
                    'name' => $order->getCustomerName(),
                    'products' => $order->getItemsCollection()
                )
            );
        }

        return $this;

    }

    /**
     * Extract "copy to" Emails
     *
     * @param string $configPath
     * @return mixed
     */
    public function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

}
