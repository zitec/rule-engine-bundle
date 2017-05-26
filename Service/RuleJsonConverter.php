<?php

namespace Zitec\RuleEngineBundle\Service;

/**
 * Class ExpressionConverter
 * Converts an array of conditions to an expression language string.
 */
class RuleJsonConverter
{
    protected const ITEM_GROUP = 'group';
    protected const ITEM_CONDITION = 'cond';

    protected const NODE_AND = 'all';
    protected const NODE_OR = 'any';
    protected const NODE_NEITHER = 'none';

    protected const NODE_NAME = 'name';
    protected const NODE_OPERATOR = 'operator';
    protected const NODE_VALUE = 'value';

    /**
     * @param string $json
     * @param RuleConditionsManager $ruleManager
     * @return array
     */
    public function formatForDisplay(string $json, RuleConditionsManager $ruleManager): array
    {
        $rule = $json ? json_decode($json, true) : [];
        if (empty($rule)) {
            return [];
        }
        foreach ($rule['items'] as &$item) {
            $this->formatItemForDisplay($item, $ruleManager);
        }

        return [
            'item_type' => $this::ITEM_GROUP,
            'data'      => $rule,
        ];
    }

    /**
     * @param array $item
     * @param RuleConditionsManager $ruleManager
     */
    protected function formatItemForDisplay(array &$item, RuleConditionsManager $ruleManager)
    {
        switch ($item['item_type']) {
            case $this::ITEM_GROUP:
                foreach ($item['data']['items'] as &$subItem) {
                    $this->formatItemForDisplay($subItem, $ruleManager);
                }
                break;
            case $this::ITEM_CONDITION:
                $item['data'] = $ruleManager->getCondition($item['data'][$this::NODE_NAME])
                        ->getDisplayValue($item['data'][$this::NODE_OPERATOR], $item['data'][$this::NODE_VALUE]);
                break;
        }
    }

    /**
     * @param string $json
     * @param RuleConditionsManager $ruleManager
     * @return string
     */
    public function generateExpression(string $json, RuleConditionsManager $ruleManager)
    {
        $rule = $json ? json_decode($json, true) : [];
        $stringExpression = '';
        if (!empty($rule)) {
            $stringExpression .= $this->getGroupExpressionPart($rule, $ruleManager);
        }

        return $stringExpression ?: 'true';
    }

    /**
     * @param array $group
     * @param RuleConditionsManager $ruleManager
     * @return string
     */
    protected function getGroupExpressionPart(array $group, RuleConditionsManager $ruleManager)
    {
        $stringExpression = '';
        $prefixExpression = '';
        switch ($group['logic_operator']) {
            case self::NODE_OR:
                $logicalOperator = ' or ';
                break;
            case self::NODE_AND:
                $logicalOperator = ' and ';
                break;
            case self::NODE_NEITHER:
                $logicalOperator = ' and ';
                $prefixExpression = ' not ';
                break;
        }

        $expressionParts = [];
        foreach ($group['items'] as $item) {
            switch ($item['item_type']) {
                case $this::ITEM_GROUP:
                    $expressionParts[] = $this->getGroupExpressionPart($item['data'], $ruleManager);
                    break;
                case $this::ITEM_CONDITION:
                    $name = $item['data'][$this::NODE_NAME];
                    $operator = $item['data'][$this::NODE_OPERATOR];
                    $value = $item['data'][$this::NODE_VALUE];
                    $contextObjectName = $ruleManager->getContext()->getContextObjectKey();
                    $methodName = $ruleManager->getContext()->getMethodName($name);
                    $expressionParts[] = $ruleManager->getCondition($name)
                        ->getExpression("$contextObjectName.$methodName()", $operator, $value);
                    break;
            }
        }
        if ($expressionParts) {
            $stringExpression .= ($prefixExpression . '(' . implode($logicalOperator, $expressionParts) . ')');
        }

        return $stringExpression;
    }
}
