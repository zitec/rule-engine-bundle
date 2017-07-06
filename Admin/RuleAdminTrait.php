<?php

namespace Zitec\RuleEngineBundle\Admin;

use Zitec\RuleEngineBundle\DoctrineBehaviors\RuleInterface;
use Zitec\RuleEngineBundle\Service\RuleConditionsManager;
use Zitec\RuleEngineBundle\Service\RuleJsonConverter;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class RuleAdminTrait
 * A trait that should be used in Sonata Admin classes for RuleEngine-aware entities.
 */
trait RuleAdminTrait
{

    /**
     * @param RuleInterface $subject
     * @return array
     */
    public function formatRule(RuleInterface $subject)
    {
        $ruleManager = $this->getRuleManager($subject);
        $formatter = new RuleJsonConverter();
        return $formatter->formatForDisplay($subject->getRule()->getJson(), $ruleManager);
    }

    /**
     * Returns the rule manager service instance for the given entity.
     *
     * @param RuleInterface|null $entity
     * @return RuleConditionsManager
     */
    protected function getRuleManager(RuleInterface $entity = null): RuleConditionsManager
    {
        $entity = $entity ?? $this->getSubject();
        return $this->getConfigurationPool()->getContainer()
            ->get('rule_engine.orchestrator')->getConditionsManagerForEntity($entity);
    }

    /**
     * @param FormMapper $formMapper
     * @param array $options
     */
    protected function addRuleFormElement(FormMapper $formMapper, array $options = [])
    {
        $ruleManager = $this->getRuleManager();
        $options = $options + ['label' => false];
        $formMapper->add('rule', 'sonata_type_admin', $options, ['rule_manager' => $ruleManager]);
    }

    /**
     * @param ListMapper $list
     */
    protected function addRuleListColumns(ListMapper $list)
    {
        $list->add('rule.name')
            ->add('rule.active', 'boolean', ['editable' => true])
            ->add('rule', null, ['template' => 'ZitecRuleEngineBundle:Admin:json_field.html.twig']);
        // In dev environment, display the expression too.
        if ($this->getConfigurationPool()->getContainer()->has('kernel.debug')) {
            $list->add('rule.expression', null, ['header_style' => 'width: 20%; text-align: center']);
        }
    }
}
