<?php

namespace Zitec\RuleEngineBundle\Service;

/**
 * This is an example of a context that supports basic global parameters.
 * A real life context would have one or more data sources that it would use read data from.
 */
class ContextBase implements RuleContextInterface
{

    /**
     * @return string
     */
    public function getContextObjectKey(): string
    {
        return RuleContextInterface::CONTEXT;
    }

    /**
     * @param string $string
     * @return string
     */
    public function getMethodName(string $string): string
    {
        return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * @param string $name
     * @return mixed
     */
    function __get($name)
    {
        $method = $this->getMethodName($name);
        $value = $this->$method();

        return $value;
    }

    /**
     * @return integer
     */
    public function getCurrentDate(): int
    {
        return time();
    }

    /**
     * @return integer
     */
    public function getCurrentDay(): int
    {
        return date('w');
    }

    /**
     * @return integer
     */
    public function getCurrentTime(): int
    {
        return strtotime('1970-01-01 ' . date('H:i:s'));
    }
}
