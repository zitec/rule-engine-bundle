<?php

namespace Zitec\RuleEngineBundle\Service;

use Doctrine\ORM\EntityManager;
use Zitec\RuleEngineBundle\DoctrineBehaviors\RuleInterface;

/**
 * Class RuleEngineOrchestrator
 *
 * @package Zitec\RuleEngineBundle\Service
 */
class Orchestrator
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RuleConditionsManager[]
     */
    protected $conditionsManagers;

    /**
     * RuleEngineOrchestrator constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityName
     * @param RuleConditionsManager $conditionsManager
     */
    public function setEntityConditionsManager(string $entityName, RuleConditionsManager $conditionsManager)
    {
        $entityClassName = $this->entityManager->getMetadataFactory()->getMetadataFor($entityName)->getName();
        $this->conditionsManagers[$entityClassName] = $conditionsManager;
    }

    /**
     * @param RuleInterface $entity
     * @return RuleConditionsManager
     */
    public function getConditionsManagerForEntity(RuleInterface $entity)
    {
        $entityClassName = get_class($entity);
        if (!isset($this->conditionsManagers[$entityClassName])) {
            throw new \InvalidArgumentException(sprintf('No conditions manager defined for entity %s', $entityClassName));
        }

        return $this->conditionsManagers[$entityClassName];
    }
}
