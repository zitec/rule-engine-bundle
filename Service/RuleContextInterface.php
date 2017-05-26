<?php

namespace Zitec\RuleEngineBundle\Service;

/**
 * Interface for a rule expression context object.
 */
interface RuleContextInterface
{
    const CONTEXT = 'context';

    /**
     * @return string
     */
    public function getContextObjectKey(): string;

    /**
     * @param string $parameterName
     * @return string
     */
    public function getMethodName(string $parameterName): string;
}
