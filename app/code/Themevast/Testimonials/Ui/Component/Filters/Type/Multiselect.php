<?php
namespace Themevast\Testimonials\Ui\Component\Filters\Type;

class Multiselect extends \Magento\Ui\Component\Filters\Type\Select
{
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];
            $conditionType = 'finset';

            if (!empty($value) || is_numeric($value)) {
                $filter = $this->filterBuilder->setConditionType($conditionType)
                    ->setField($this->getName())
                    ->setValue($value)
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }


}