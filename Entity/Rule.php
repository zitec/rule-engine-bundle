<?php

namespace Zitec\RuleEngineBundle\Entity;

use Zitec\RuleEngineBundle\Form\RuleObject;

/**
 * Class Rule
 *
 * @package RuleEngineBundle\Entity
 */
class Rule
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var string
     */
    protected $expression = '';

    /**
     * @var string
     */
    protected $json = '';

    /**
     * Object that form field returns
     * @var RuleObject
     */
    protected $ruleObject = null;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }


    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Rule
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Returns expression in expression language format
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param null|string $expression
     * @return Rule
     */
    public function setExpression(?string $expression)
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param null|string $json
     * @return Rule
     */
    public function setJson(?string $json)
    {
        $this->json = $json;
        return $this;
    }

    /**
     * Set the object that contains expression language and json
     * @param RuleObject $ruleObject
     * @return $this
     */
    public function setRuleObject(RuleObject $ruleObject)
    {
        if ($ruleObject) {
            $this->ruleObject = $ruleObject;
            $this->json = $ruleObject->getJson();
            $this->expression = $ruleObject->getExpression();
        }
        return $this;
    }

    /**
     * Returns the rule object
     * @return null|RuleObject
     */
    public function getRuleObject()
    {
        if (is_null($this->ruleObject)) {
            $this->ruleObject = new RuleObject();
            $this->ruleObject->setExpression($this->expression);
            $this->ruleObject->setJson($this->json);
        }
        return $this->ruleObject;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->name) {
            return $this->name;
        }
        return '';
    }
}
