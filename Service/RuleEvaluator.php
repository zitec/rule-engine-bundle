<?php

namespace Zitec\RuleEngineBundle\Service;

use Zitec\RuleEngineBundle\Entity\Rule;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class RuleEvaluator
 * Rule expression evaluator that uses the ExpressionLanguage service to evaluate rules.
 */
class RuleEvaluator
{
    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;

    /**
     * RuleEvaluator constructor.
     */
    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage(new FilesystemAdapter('RuleEngine'));
    }

    /**
     * @param ExpressionFunctionProviderInterface $provider
     */
    public function addExpressionFunctionProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguage->registerProvider($provider);
    }

    /**
     * @param Rule $rule
     * @param RuleContextInterface $contextObject
     * @return boolean
     */
    public function evaluate(Rule $rule, RuleContextInterface $contextObject): bool
    {
        $expression = $rule->getExpression();
        $values = [$contextObject->getContextObjectKey() => $contextObject];

        return $rule->getActive() && $this->expressionLanguage->evaluate($expression, $values);
    }
}
