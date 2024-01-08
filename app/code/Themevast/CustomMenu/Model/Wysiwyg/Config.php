<?php

namespace Themevast\CustomMenu\Model\Wysiwyg;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;

class Config extends \Magento\Cms\Model\Wysiwyg\Config
{
    public function getConfig($data = [])
    {
        $config = new \Magento\Framework\DataObject();

        $config->setData(
            [
                'enabled' => $this->isEnabled(),
                'hidden' => $this->isHidden(),
                'use_container' => false,
                'add_variables' => true,
                'add_widgets' => true,
                'no_display' => false,
                'encode_directives' => true,
                'baseStaticUrl' => $this->_assetRepo->getStaticViewFileContext()->getBaseUrl(),
                'baseStaticDefaultUrl' => str_replace('index.php/', '', $this->_backendUrl->getBaseUrl())
                    . $this->filesystem->getUri(DirectoryList::STATIC_VIEW) . '/',
                'directives_url' => $this->_backendUrl->getUrl('cms/wysiwyg/directive'),
                'popup_css' => $this->_assetRepo->getUrl(
                    'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css'
                ),
                'content_css' => $this->_assetRepo->getUrl(
                    'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css'
                ),
                'width' => '100%',
				'add_directives' => true,
                'height' => '500px',
                'plugins' => [],
            ]
        );

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if ($this->_authorization->isAllowed('Magento_Cms::media_gallery')) {
            $config->addData(
                [
                    'add_images' => true,
                    'files_browser_window_url' => $this->_backendUrl->getUrl('cms/wysiwyg_images/index'),
                    'files_browser_window_width' => $this->_windowSize['width'],
                    'files_browser_window_height' => $this->_windowSize['height'],
                ]
            );
        }

        if (is_array($data)) {
            $config->addData($data);
        }

        if ($config->getData('add_variables')) {
            $settings = $this->_variableConfig->getWysiwygPluginSettings($config);
            $config->addData($settings);
        }

        if ($config->getData('add_widgets')) {
            $settings = $this->_widgetConfig->getPluginSettings($config);
            $config->addData($settings);
        }

        return $config;
    }
}
