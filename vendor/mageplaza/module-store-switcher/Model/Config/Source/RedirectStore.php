<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RedirectStore
 * @package Mageplaza\StoreSwitcher\Model\Config\Source
 */
class RedirectStore implements ArrayInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * RedirectStore constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storeManagerDataList = $this->_storeManager->getStores();
        $options              = [];

        $options[] = [
            'label' => '--',
            'value' => ''
        ];

        /** @var array $value */
        foreach ($storeManagerDataList as $value) {
            $options[] = ['label' => $value['name'], 'value' => $value->getId()];
        }

        return $options;
    }
}
