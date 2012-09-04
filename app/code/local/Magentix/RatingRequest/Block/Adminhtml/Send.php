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

class Magentix_RatingRequest_Block_Adminhtml_Send extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected $_addRowButtonHtml = array();

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        return $this->_getAddRowButtonHtml('send_container', $this->__('Send'));
    }

    protected function _getAddRowButtonHtml($container, $title = 'Send')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setClass('add ' . $this->_getDisabled())
                ->setLabel($this->__($title))
                ->setOnClick("setLocation('" . Mage::helper("adminhtml")->getUrl('*/email/test') . "')")
                ->setDisabled($this->_getDisabled())
                ->toHtml();
        }

        return $this->_addRowButtonHtml[$container];
    }

    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

}