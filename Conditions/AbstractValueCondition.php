<?php

namespace Zitec\RuleEngineBundle\Conditions;

/**
 * Starter kit class for declaring conditions for a single-value parameter.
 */
abstract class AbstractValueCondition implements ConditionInterface
{

    /**
     * Use this operator when you want to
     * check that the parameter value is among a given set of values.
     */
    protected const VALUE_IN = 'valueIn';

    /**
     * Use this operator when you want to
     * check that the parameter value is NOT among a given set of values.
     */
    protected const VALUE_NOT_IN = 'valueNotIn';

    /**
     * Use this operator when you want to
     * check that the parameter value is in a given numeric interval.
     */
    protected const INTERVAL = 'valueInterval';

    /**
     * Use this operator when you want to
     * check that the parameter value and configured value are equal.
     */
    protected const EQUALS = 'valueEqual';

    /**
     * Use this operator when you want to
     * check that the parameter value is greater than the configured value.
     */
    protected const GREATER = 'valueGreater';

    /**
     * Use this operator when you want to
     * compare that the parameter value is smaller than the configured value.
     */
    protected const SMALLER = 'valueSmaller';

    /**
     * Use this operator when you want to
     * compare the parameter value with another single-value parameter's value.
     */
    protected const EQUALS_PARAM = 'valueEqParam';

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
     * {@inheritdoc}
     *
     * Example implementation can be found in the CurrentDate parameter implementation.
     *
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
            case $this::VALUE_IN:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode("','", $value);

                return "$parameterName in ['$formattedValue']";
            case $this::VALUE_NOT_IN:
                if (!is_array($value)) {
                    throw new \UnexpectedValueException('The value for the %s operator has to be an array.', $operator);
                }
                $formattedValue = implode("','", $value);

                return "$parameterName not in ['$formattedValue']";
            case $this::INTERVAL:
                if (!isset($value['from']) || !isset($value['to']) || $value['to'] < $value['from']) {
                    throw new \UnexpectedValueException(
                        'The value for the %s operator is not a valid interval.',
                        $operator
                    );
                }
                $start = $value['from'];
                $end = $value['to'];

                return "$parameterName >= $start and $parameterName <= $end";
            case $this::EQUALS:
                return "$parameterName == $value";
            case $this::GREATER:
                return "$parameterName > $value";
            case $this::SMALLER:
                return "$parameterName < $value";
            case $this::EQUALS_PARAM:
                return "$parameterName == $value";
        }

        throw new \DomainException(sprintf('Unrecognized operator %s', $operator));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validateParameterValue($value): bool
    {
        return is_string($value);
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
            case $this::VALUE_IN:
            case $this::VALUE_NOT_IN:
                $formattedValue = implode(',', $value);
                break;
            case $this::INTERVAL:
                $start = $value['from'];
                $end = $value['to'];
                $formattedValue = "$start and $end";
                break;
            case $this::EQUALS:
            case $this::GREATER:
            case $this::SMALLER:
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
