<?php

namespace Zitec\RuleEngineBundle\Service;

use Zitec\RuleEngineBundle\Entity\Rule;

/**
 * Interface RuleEvaluatorInterface
 */
interface RuleEvaluatorInterface
{

    /**
     * @param Rule $rule
     * @param RuleContextInterface $contextObject
     * @return bool
     */
    public function evaluate(Rule $rule, RuleContextInterface $contextObject): bool;
}
