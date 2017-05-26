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
class RuleEnginePass implements CompilerPassInterface
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
        $this->container = $container;
        $this->processExpressionProviders();
        $this->processAutocomplete();
    }

    /**
     * Adds expression language functions providers to the rule engine evaluator service.
     */
    protected function processExpressionProviders()
    {
        if (!$this->container->has('rule_engine.evaluator')) {
            return;
        }
        $evaluator = $this->container->findDefinition('rule_engine.evaluator');
        $providers = $this->container->findTaggedServiceIds('rule_engine.expression_function_provider');
        foreach (array_keys($providers) as $id) {
            $evaluator->addMethodCall('addExpressionFunctionProvider', [new Reference($id)]);
        }
    }

    /**
     * Register autocomplete data sources.
     */
    protected function processAutocomplete()
    {
        if (!$this->container->has('rule_engine.autocomplete')) {
            return;
        }

        $dataSources = [];
        $dataSourceServices = $this->container->findTaggedServiceIds('rule_engine.autocomplete.data_source');
        foreach ($dataSourceServices as $serviceId => $tags) {

            foreach ($tags as $attributes) {
                $dataSourceKey = $attributes['key'];
                if (isset($dataSources[$dataSourceKey])) {
                    throw new LogicException(sprintf('Duplicate data source key "%s" found!', $dataSourceKey));
                }
            }

            $dataSources[$dataSourceKey] = new Reference($serviceId);
        }

        $definition = $this->container->findDefinition('rule_engine.autocomplete');
        $definition->setArguments([$dataSources]);
    }
}
