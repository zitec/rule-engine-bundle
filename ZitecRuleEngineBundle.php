<?php

namespace Zitec\RuleEngineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zitec\RuleEngineBundle\DependencyInjection\Compiler\AutocompletePass;
use Zitec\RuleEngineBundle\DependencyInjection\Compiler\ConditionsManagerPass;
use Zitec\RuleEngineBundle\DependencyInjection\Compiler\ExpressionProviderPass;

/**
 * Class RuleEngineBundle
 */
class ZitecRuleEngineBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AutocompletePass());
        $container->addCompilerPass(new ExpressionProviderPass());
        $container->addCompilerPass(new ConditionsManagerPass());
        parent::build($container);
    }
}
