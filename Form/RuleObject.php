<?php

namespace Zitec\RuleEngineBundle\Form;

/**
 * Class RuleObject
 * Object that holds a json and an expression language string, mapping them on a single form type.
 */
class RuleObject
{

    /**
     * @var string
     */
    protected $json;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param mixed $json
     *
     * @return RuleObject
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param mixed $expression
     * @return RuleObject
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;

        return $this;
    }
}
