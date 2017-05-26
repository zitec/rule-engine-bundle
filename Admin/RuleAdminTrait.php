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
     * @var RuleConditionsManager[]
     */
    private $ruleManagers;

    /**
     * Method should be used in sonata admin service declaration for single class admins.
     *
     * @param RuleConditionsManager $ruleManager
     */
    public function setRuleManager(RuleConditionsManager $ruleManager): void
    {
        $this->ruleManagers[] = $ruleManager;
    }

    /**
     * Method should be used in sonata admin service declaration for multi-class (subclasses) admins
     *
     * @param RuleConditionsManager[] $ruleManagers
     */
    public function setSubManagers(array $ruleManagers): void
    {
        $this->ruleManagers = $ruleManagers;
    }

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
     * Returns an array of supported conditions to be used in the rule entity form based on the supported context.
     * Defaults to all conditions.
     *
     * @param RuleInterface|null $subject
     * @return RuleConditionsManager
     */
    protected function getRuleManager(RuleInterface $subject = null): RuleConditionsManager
    {
        if (count($this->ruleManagers) == 1) {
            $ruleManager = reset($this->ruleManagers);
        } else {
            $subject = $subject ?? $this->getSubject();
            $subclasses = $this->getSubClasses();
            $activeSubclass = get_class($subject);
            $subClassCode = array_search($activeSubclass, $subclasses);
            $ruleManager = $this->ruleManagers[$subClassCode] ?? null;
        }
        if (!isset($ruleManager)) {
            throw new \RuntimeException('Missing context object for this admin.');
        }

        return $ruleManager;
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
            ->add('rule', null, ['template' => 'RuleEngineBundle:Admin:json_field.html.twig']);
        // In dev environment, display the expression too.
        if ($this->getConfigurationPool()->getContainer()->get('kernel.debug')) {
            $list->add('rule.expression', null, ['header_style' => 'width: 20%; text-align: center']);
        }
    }
}
