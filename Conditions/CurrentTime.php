<?php

namespace Zitec\RuleEngineBundle\Conditions;

/**
 * Class CurrentTime
 */
class CurrentTime extends AbstractValueCondition
{
    /**
     * @var string
     */
    protected $name = 'current_time';
    /**
     * @var string
     */
    protected $label = 'Current Time';
    /**
     * @var string
     */
    protected $description = 'Conditions for the current time';

    /**
     * @return array
     */
    protected function getOperatorDefinitions(): array
    {
        return [
            [
                'label'           => 'Time between',
                'name'            => $this::INTERVAL,
                'fieldType'       => 'datetime_interval',
                'fieldOptions'    => [
                    'datetimepicker' => [
                        'pickDate' => false,
                        'pickTime' => true,
                    ],
                ],
                'value_transform' => function ($val) {
                    return [
                        'from' => strtotime('1970-01-01 ' . $val['from']),
                        'to'   => strtotime('1970-01-01 ' . $val['to']),
                    ];
                },
            ],
        ];
    }
}
