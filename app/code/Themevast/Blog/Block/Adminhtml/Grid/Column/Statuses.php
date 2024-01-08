<?php
namespace Themevast\Blog\Block\Adminhtml\Grid\Column;

class Statuses extends \Magento\Backend\Block\Widget\Grid\Column
{
   
    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
    }

    public function decorateStatus($value, $row, $column, $isExport)
    {
        if ($row->getIsActive() || $row->getStatus()) {
            $cell = '<span class="grid-severity-notice"><span>' . $value . '</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>' . $value . '</span></span>';
        }
        return $cell;
    }
}
