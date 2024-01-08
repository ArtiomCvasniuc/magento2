<?php
/**
 * Copyright © 2016 Themevast. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Themevast\MegaMenu\Setup;
use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * @var Setup\SampleData\Executor
     */
	private $menu;
	
	public function __construct(
        \Themevast\MegaMenu\Model\MenuData $menu
    ) {
        $this->menu = $menu;
    }
	/**
     * {@inheritdoc}
     */
    public function install()
    {
		$this->menu->install([
			'Themevast_MegaMenu::fixtures/themevast_megamenu.csv'
		]);
	}
}