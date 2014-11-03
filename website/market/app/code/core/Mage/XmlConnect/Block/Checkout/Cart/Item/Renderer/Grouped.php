<?php
/**
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart grouped item render block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Xmlconnect_Block_Checkout_Cart_Item_Renderer_Grouped extends Mage_Checkout_Block_Cart_Item_Renderer_Grouped
{
    /**
     * Get product thumbnail image
     *
     * @return Mage_XmlConnect_Helper_Catalog_Product_Image
     */
    public function getProductThumbnail()
    {
        $product = $this->getProduct();
        if (!$product->getData('thumbnail')
            ||($product->getData('thumbnail') == 'no_selection')
            || (Mage::getStoreConfig(self::GROUPED_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)
        ) {
            $product = $this->getGroupedProduct();
        }
        return $this->helper('xmlconnect/catalog_product_image')->init($product, 'thumbnail');
    }
}
