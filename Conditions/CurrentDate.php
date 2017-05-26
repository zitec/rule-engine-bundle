<?php

namespace Zitec\RuleEngineBundle\Conditions;

/**
 * Class CurrentDate
 */
class CurrentDate extends AbstractValueCondition
{
    /**
     * @var string
     */
    protected $name = 'current_date';
    /**
     * @var string
     */
    protected $label = 'Current Date';
    /**
     * @var string
     */
    protected $description = 'Conditions for the current date/time';

    /**
     * @return array
     */
    protected function getOperatorDefinitions(): array
    {
        return [
            [
                'label'           => 'date is after',
                'name'            => $this::GREATER,
                'fieldType'       => 'datetime',
                'value_transform' => function ($val) {
                    return strtotime($val . ' + 1 day');
                },
            ],
            [
                'label'           => 'date is before',
                'name'            => $this::SMALLER,
                'fieldType'       => 'datetime',
                'value_transform' => function ($val) {
                    return strtotime($val);
                },
            ],
            [
                'label'           => 'Date between',
                'name'            => $this::INTERVAL,
                'fieldType'       => 'datetime_interval',
                'value_transform' => function ($val) {
                    return isset($val['from']) && isset($val['to']) ?
                        ['from' => strtotime($val['from']), 'to' => strtotime($val['to'])] : null;
                },
            ],
        ];
    }
}
