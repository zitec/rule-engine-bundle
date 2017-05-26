<?php

namespace Zitec\RuleEngineBundle\Service;

use Zitec\RuleEngineBundle\Conditions\ConditionInterface;

/**
 * Class RuleConditionsManager
 */
class RuleConditionsManager
{

    /**
     * @var ConditionInterface[]
     */
    protected $conditions = [];

    /**
     * @var RuleContextInterface
     */
    protected $context;

    /**
     * RuleConditionsManager constructor.
     *
     * @param RuleContextInterface $context
     */
    public function __construct(RuleContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @param ConditionInterface $condition
     */
    public function addSupportedCondition(ConditionInterface $condition): void
    {
        $key = $condition->getName();
        if (isset($this->conditions[$key])) {
            throw new \InvalidArgumentException('A condition with the same machine name was already added!');
        }
        if (!is_callable([$this->getContext(), $this->getContext()->getMethodName($key)])) {
            throw new \InvalidArgumentException(sprintf('Missing getter for %s condition', $key));
        }
        $this->conditions[$key] = $condition;
    }

    /**
     * @return ConditionInterface[]
     */
    public function getSupportedConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getCondition($name): ConditionInterface
    {
        if (!isset($this->conditions[$name])) {
            throw new \InvalidArgumentException('Condition %s not supported by context.', $name);
        }

        return $this->conditions[$name];
    }

    /**
     * @return RuleContextInterface
     */
    public function getContext(): RuleContextInterface
    {
        return $this->context;
    }
}
