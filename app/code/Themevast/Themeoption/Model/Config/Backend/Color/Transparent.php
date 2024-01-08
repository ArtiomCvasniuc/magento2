<?php

namespace Themevast\Themeoption\Model\Config\Backend\Color;

class Transparent extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $v = $this->getValue();
        if ($v == 'rgba(0, 0, 0, 0)')
        {
            $this->setValue('transparent');
        }
        return $this;
    }
}
