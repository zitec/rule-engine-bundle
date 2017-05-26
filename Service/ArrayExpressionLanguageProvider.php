<?php

namespace Zitec\RuleEngineBundle\Service;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Provides functions for comparing array values in expressions.
 */
class ArrayExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'disjoint',
                function () {
                    return 'empty(array_intersect($parameter, explode(\',\', $value)))';
                },
                function ($variables, $parameter, $value) {
                    return empty(array_intersect($parameter, explode(',', $value)));
                }
            ),
            new ExpressionFunction(
                'intersecting',
                function () {
                    return '!empty(array_intersect($parameter, explode(\',\', $value)))';
                },
                function ($variables, $parameter, $value) {
                    return !empty(array_intersect($parameter, explode(',', $value)));
                }
            ),
            new ExpressionFunction(
                'included',
                function () {
                    return 'empty(array_diff($parameter, explode(\',\', $value)))';
                },
                function ($variables, $parameter, $value) {
                    return empty(array_diff($parameter, explode(',', $value)));
                }
            ),
            new ExpressionFunction(
                'identical',
                function () {
                    return '$values = is_array($value) ? $value : explode(\',\', $value);
                    return empty(array_diff($parameter, $values)) && empty(array_diff($values, $parameter));';
                },
                function ($variables, $parameter, $value) {
                    $values = is_array($value) ? $value : explode(',', $value);

                    return empty(array_diff($parameter, $values)) && empty(array_diff($values, $parameter));
                }
            ),
            new ExpressionFunction(
                'includes',
                function () {
                    return 'empty(array_diff(explode(\',\', $value), $parameter))';
                },
                function ($variables, $parameter, $value) {
                    return empty(array_diff(explode(',', $value), $parameter));
                }
            ),
            new ExpressionFunction(
                'uniqount',
                function () {
                    return 'count(array_unique($value))';
                },
                function ($variables, $value) {
                    return count(array_unique($value));
                }
            ),
        ];
    }
}
