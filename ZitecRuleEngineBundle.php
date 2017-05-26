<?php

namespace Zitec\RuleEngineBundle;

use Zitec\RuleEngineBundle\DependencyInjection\Compiler\RuleEnginePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
        $container->addCompilerPass(new RuleEnginePass());
        parent::build($container);
    }
}
