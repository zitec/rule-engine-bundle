<?php

namespace Zitec\RuleEngineBundle\Conditions;

/**
 * Starter kit class for declaring conditions for a multi-value parameter.
 */
abstract class AbstractArrayCondition implements ConditionInterface
{

    /**
     * Use this operator when you want to
     * check that the parameter value set and configured value set have at least one element in common.
     */
    protected const INTERSECTING = 'isIn';

    /**
     * Use this operator when you want to
     * check that the parameter value set and configured value set have no elements in common.
     */
    protected const DISJOINT = 'isNotIn';

    /**
     * Use this operator when you want to
     * check that the parameter value set includes all configured values.
     */
    protected const INCLUDES = 'isAll';

    /**
     * Use this operator when you want to
     * check that the parameter value and configured value have exactly the same elements.
     */
    protected const IDENTICAL = 'isExact';

    /**
     * Use this operator when you want to
     * check that the parameter value set has no values outside the configured value set, ie. is included in it.
     */
    protected const INCLUDED = 'isOnly';

    /**
     * Use this operator when you want to
     * compare the number of unique elements in the  parameter value set.
     */
    protected const COUNT = 'countIs';

    /**
     * Use this operator when you want to
     * compare the parameter value with another multi-value (array) parameter's value.
     */
    protected const EQUALS_PARAM = 'eqParam';

    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $label;
    /**
     * @var string
     */
    protected $description;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getDefinitions(): array
    {
        return [
            'name'        => $this->name,
            'label'       => $this->label,
            'description' => $this->description,
            'operators'   => $this->getOperatorDefinitions(),
        ];
    }

    /**
     * @return array
     */
    abstract protected function getOperatorDefinitions(): array;

    /**
     * @param string $parameterName
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    public function getExpression(string $parameterName, string $operator, $value): string
    {
        if (is_null($value)) {
            throw new \UnexpectedValueException(
                sprintf('NULL value received for condition %s and operator $s', $this->name, $operator)
            );
        }

        $operators = $this->getOperatorDefinitions();
        foreach ($operators as $definition) {
            if ($operator == $definition['name'] && isset($definition['value_transform'])) {
                $value = call_user_func($definition['value_transform'], $value);
                break;
            }
        }

        switch ($operator) {
            case $this::INTERSECTING:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode(',', $value);

                return "intersecting($parameterName, '$formattedValue')";
            case $this::DISJOINT:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode(',', $value);

                return "disjoint($parameterName, '$formattedValue')";
            case $this::INCLUDES:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode(',', $value);

                return "includes($parameterName, '$formattedValue')";
            case $this::IDENTICAL:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode(',', $value);

                return "identical($parameterName, '$formattedValue')";
            case $this::INCLUDED:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode(',', $value);

                return "included($parameterName, '$formattedValue')";
            case $this::COUNT:
                if (!is_numeric($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be a number.', $operator);
                }

                return "uniqount($parameterName) == $value";
            case $this::EQUALS_PARAM:
                if (!is_string($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be a string.', $operator);
                }

                return "$parameterName == $parameterName.$value";
        }

        throw new \DomainException(sprintf('Unrecognized operator %s', $operator));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validateParameterValue($value): bool
    {
        return is_array($value);
    }

    /**
     * @param string $operator
     * @param $value
     * @return string
     */
    public function getDisplayValue(string $operator, $value): string
    {
        $operatorLabel = '';
        $operators = $this->getOperatorDefinitions();
        foreach ($operators as $definition) {
            if ($operator == $definition['name']) {
                $operatorLabel = $definition['label'];
                if (isset($definition['value_view_transform'])) {
                    $value = call_user_func($definition['value_view_transform'], $value);
                }
                break;
            }
        }
        switch ($operator) {
            case $this::INTERSECTING:
            case $this::DISJOINT:
            case $this::INCLUDES:
            case $this::IDENTICAL:
            case $this::INCLUDED:
                $formattedValue = implode(',', $value);
                break;
            case $this::COUNT:
            case $this::EQUALS_PARAM:
                $formattedValue = $value;
                break;
            default:
                $formattedValue = '';
                break;
        }

        return $this->label . ': ' . $operatorLabel . ' ' . $formattedValue;
    }
}
