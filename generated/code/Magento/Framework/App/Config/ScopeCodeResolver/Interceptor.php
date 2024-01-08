<?php
namespace Magento\Framework\App\Config\ScopeCodeResolver;

/**
 * Interceptor class for @see \Magento\Framework\App\Config\ScopeCodeResolver
 */
class Interceptor extends \Magento\Framework\App\Config\ScopeCodeResolver implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ScopeResolverPool $scopeResolverPool)
    {
        $this->___init();
        parent::__construct($scopeResolverPool);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($scopeType, $scopeCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        if (!$pluginInfo) {
            return parent::resolve($scopeType, $scopeCode);
        } else {
            return $this->___callPlugins('resolve', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clean');
        if (!$pluginInfo) {
            return parent::clean();
        } else {
            return $this->___callPlugins('clean', func_get_args(), $pluginInfo);
        }
    }
}
