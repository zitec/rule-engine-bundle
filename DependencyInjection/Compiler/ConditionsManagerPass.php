<?php

namespace Zitec\RuleEngineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ConditionsManagerPass
 */
class ConditionsManagerPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('rule_engine.orchestrator')) {
            return;
        }
        $evaluator = $container->findDefinition('rule_engine.orchestrator');
        $conditionsManagers = $container->findTaggedServiceIds('rule_engine.conditions_manager');
        $entities = [];
        foreach ($conditionsManagers as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (isset($attributes['entity'])) {
                    if (isset($entities[$attributes['entity']])) {
                        throw new LogicException(
                            sprintf('Duplicate conditions manager for entity "%s" found!', $attributes['entity'])
                        );
                    }
                    $entities[$attributes['entity']] = $attributes['entity'];
                    $evaluator->addMethodCall(
                        'setEntityConditionsManager',
                        [$attributes['entity'], new Reference($serviceId)]
                    );

                }
            }
        }
    }
}
