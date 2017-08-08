<?php

namespace Zitec\RuleEngineBundle\DoctrineBehaviors;

use Zitec\RuleEngineBundle\Entity\Rule;
use Zitec\RuleEngineBundle\Service\RuleContextInterface;

/**
 * Class RuleTrait
 * A trait that can be used in an entity class to add rule behavior to it.
 *  It is recommended that you use it in conjecture with RuleInterface.
 */
trait RuleTrait
{

    /**
     * @var Rule
     */
    private $rule;

    /**
     * @var RuleContextInterface
     */
    private $contextObject;

    /**
     * Set rule
     *
     * @param null|Rule $rule
     *
     * @return self
     */
    public function setRule(?Rule $rule = null)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule
     *
     * @return Rule
     */
    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    /**
     * @return RuleContextInterface
     */
    public function getContextObject(): RuleContextInterface
    {
        return $this->contextObject;
    }

    /**
     * @param RuleContextInterface $contextObject
     *
     * @return self
     */
    public function setContextObject(RuleContextInterface $contextObject)
    {
        $this->contextObject = $contextObject;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $rule = $this->getRule();
        if ($rule instanceof Rule) {
            $name = $rule->getName();
            if (isset($name)){
                return $name;
            }
        }

        return "New rule";
    }
}
