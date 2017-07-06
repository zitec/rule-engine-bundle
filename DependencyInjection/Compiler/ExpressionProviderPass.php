<?php

namespace Zitec\RuleEngineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ExpressionProviderPass
 */
class ExpressionProviderPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('rule_engine.evaluator')) {
            return;
        }
        $evaluator = $container->findDefinition('rule_engine.evaluator');
        $providers = $container->findTaggedServiceIds('rule_engine.expression_function_provider');
        foreach (array_keys($providers) as $id) {
            $evaluator->addMethodCall('addExpressionFunctionProvider', [new Reference($id)]);
        }
    }
}
