<?php

namespace Zitec\RuleEngineBundle\DoctrineBehaviors;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Zitec\RuleEngineBundle\Entity\Rule;

/**
 * Class RuleSubscriber
 * Doctrine event listener service that adds required fields to entities using RuleTrait.
 */
class RuleSubscriber implements EventSubscriber
{

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }

    /**
     * Adds a oneToOne association between the Rule entity and the entity using the RuleTrait.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /* @var $classMetadata ClassMetadata */
        $classMetadata = $eventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass) {
            return;
        }
        if (in_array(RuleTrait::class, $classMetadata->reflClass->getTraitNames())) {
            if (!$classMetadata->hasField('rule')) {
                $classMetadata->mapOneToOne(
                    [
                        'targetEntity'  => Rule::class,
                        'fetch'         => ClassMetadataInfo::FETCH_EAGER,
                        'fieldName'     => 'rule',
                        'cascade'       => ['persist', 'remove'],
                        'orphanRemoval' => true,
                        'joinColumn'    => [
                            'name'                 => 'rule_id',
                            'referencedColumnName' => 'id',
                        ],
                    ]
                );
            }
        }
    }
}
