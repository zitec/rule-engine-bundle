<?php

namespace Zitec\RuleEngineBundle\Conditions;

/**
 * Class SearchDay
 */
class CurrentDay extends AbstractValueCondition
{
    /**
     * @var string
     */
    protected $name = 'current_day';
    /**
     * @var string
     */
    protected $label = 'Current Day';
    /**
     * @var string
     */
    protected $description = 'Conditions for the current day of the week';

    /**
     * @return array
     */
    protected function getOperatorDefinitions(): array
    {
        $timestamp = strtotime('next Sunday');
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = ['key' => $i, 'label' => strftime('%A', $timestamp)];
            $timestamp = strtotime('+1 day', $timestamp);
        }

        return [
            [
                'label'                => 'weekday in',
                'name'                 => $this::VALUE_IN,
                'fieldType'            => 'select',
                'fieldOptions'         => [
                    'multiple'      => true,
                    'enableSelect2' => true,
                    'options'       => $days,
                ],
                'value_view_transform' => function ($val) {
                    return array_map(
                        function ($v) {
                            $map = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

                            return $map[$v];
                        },
                        $val
                    );
                },
            ],
            [
                'label'                => 'weekday NOT in',
                'name'                 => $this::VALUE_NOT_IN,
                'fieldType'            => 'select',
                'fieldOptions'         => [
                    'multiple'      => true,
                    'enableSelect2' => true,
                    'options'       => $days,
                ],
                'value_view_transform' => function ($val) {
                    return array_map(
                        function ($v) {
                            $map = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

                            return $map[$v];
                        },
                        $val
                    );
                },
            ],
        ];
    }
}
