<?php

namespace Zitec\RuleEngineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RuleEnginePass
 * Compiler pass that aggregates expression language function providers and autocomplete implementations.
 */
class AutocompletePass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('rule_engine.autocomplete')) {
            return;
        }

        $dataSources = [];
        $dataSourceServices = $container->findTaggedServiceIds('rule_engine.autocomplete.data_source');
        foreach ($dataSourceServices as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $dataSourceKey = $attributes['key'];
                if (isset($dataSources[$dataSourceKey])) {
                    throw new LogicException(sprintf('Duplicate data source key "%s" found!', $dataSourceKey));
                }
                $dataSources[$dataSourceKey] = new Reference($serviceId);
                continue 2;
            }
        }

        $definition = $container->findDefinition('rule_engine.autocomplete');
        $definition->setArguments([$dataSources]);
    }
}
