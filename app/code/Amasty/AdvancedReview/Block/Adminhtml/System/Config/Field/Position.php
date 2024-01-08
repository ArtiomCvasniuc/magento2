<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Adminhtml\System\Config\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Amasty\AdvancedReview\Helper\Config as ReviewConfig;
use Amasty\AdvancedReview\Model\Sources\Sort;

class Position extends Field
{
    /**
     * @var ReviewConfig
     */
    private $helper;

    /**
     * @var Sort
     */
    private $sort;

    public function __construct(
        ReviewConfig $helper,
        Context $context,
        Sort $sort
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->sort = $sort;
    }

    protected function _construct()
    {
        $this->setTemplate('Amasty_AdvancedReview::position.phtml');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        $positions = (array)$this->helper->getSortOrder();
        if ($positions === []) {
            $positions = $this->getOptionalArray();
        } else {
            $availableOptions = $this->getOptionalArray();
            // delete disabled options
            $positions = array_intersect($positions, $availableOptions);
            $newOptions = array_diff($availableOptions, $positions);
            $positions = array_merge($positions, $newOptions);
        }

        return $positions;
    }

    /**
     * @param $index
     * @return string
     */
    public function getNamePrefix($index)
    {
        return $this->getElement()->getName() . '[' . $index . ']';
    }

    /**
     * @return array
     */
    private function getOptionalArray()
    {
        $positions = [];
        $methods = $this->sort->toOptionArray();
        foreach ($methods as $methodObject) {
            $positions[$methodObject['value']] = $methodObject['label'];
        }

        return $positions;
    }
}
