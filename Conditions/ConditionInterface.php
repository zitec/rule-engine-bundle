<?php

namespace Zitec\RuleEngineBundle\Conditions;

/**
 * Interface ConditionInterface
 */
interface ConditionInterface
{

    /**
     * Returns the machine name for this condition,
     * that will be used in the generated expression to identify the corresponding parameter.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the human readable name of this condition/parameter.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Returns the definitions for the property-operator pairs that this specific condition implementation supports.
     *
     * @return array
     */
    public function getDefinitions(): array;

    /**
     * Returns the ExpressionLanguage string for the given operator and value.
     *
     * @param string $parameterName
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    public function getExpression(string $parameterName, string $operator, $value): string;

    /**
     * @param string $operator
     * @param $value
     * @return string
     */
    public function getDisplayValue(string $operator, $value): string;

    /**
     * Validates the parameter value before expression evaluation, to allow better exception throwing.
     *
     * @param mixed $value
     * @return bool
     */
    public function validateParameterValue($value): bool;
}
