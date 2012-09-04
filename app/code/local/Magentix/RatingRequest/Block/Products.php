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

class Magentix_RatingRequest_Block_Products extends Mage_Core_Block_Text
{

    const XML_PATH_PRODUCT_TEMPLATE = 'ratingrequest/email/product_template';

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setCacheLifetime(null);

        if ($this->getProducts()) $this->addText($this->getProductsHtml($this->getProducts()));

        return parent::_toHtml();
    }


    /**
     * Prepare product line output
     *
     * @param Mage_Sales_Model_Mysql4_Order_Item_Collection $products
     * @param string $html
     * @return string
     */
    public function getProductsHtml($products, $html = '')
    {
        foreach ($products as $p) {
            if($this->getReviewLineTpl()) {
                $values = array(
                    'name' => $p->getName(),
                    'url' => $this->getReviewUrl($p->getProductId())
                );
                $html .= preg_replace('/{%([a-z]*)%}/e', "\$values['\\1']", $this->getReviewLineTpl());
            }
        }

        return $html;
    }

    /**
     * Retrieve Review Url
     *
     * @param int $productId
     * @return string
     */
    public function getReviewUrl($productId)
    {
        return Mage::getUrl('review/product/list', array('id' => $productId)) . "#review-form";
    }

    /**
     * Retrieve Review line template from config
     *
     * @return string
     */
    public function getReviewLineTpl()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRODUCT_TEMPLATE);
    }

}