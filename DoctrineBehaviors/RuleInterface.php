<?php

namespace Zitec\RuleEngineBundle\DoctrineBehaviors;

use Zitec\RuleEngineBundle\Entity\Rule;

/**
 * Interface RuleInterface
 * An interface for entities using the RuleTrait to allow better type hinting for implementing entities.
 */
interface RuleInterface
{
    /**
     * @return Rule|null
     */
    public function getRule(): ?Rule;
}
