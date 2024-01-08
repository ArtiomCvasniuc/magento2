<?php
namespace Themevast\MegaMenu\Controller\Adminhtml\Index;
use Themevast\MegaMenu\Controller\Adminhtml\AbstractMassStatus;
class MassEnable extends AbstractMassStatus{
	const ID_FIELD = 'menu_id';
	protected $collection = 'Themevast\MegaMenu\Model\ResourceModel\Megamenu\Collection';
	protected $model = 'Themevast\MegaMenu\Model\Megamenu';
	protected $status = true;
}