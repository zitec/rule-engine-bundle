<?php

namespace Zitec\RuleEngineBundle\Autocomplete;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Abstract class that provides basic autocomplete functionality for doctrine entities.
 * Should be extended by autocomplete service implementations.
 */
abstract class AbstractAutocompleteEntity implements AutocompleteInterface
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Entity class name, to be used by the query builder.
     *
     * @var string
     */
    protected $entity;

    /**
     * Name of the entity field used for the id parameter of the autocomplete results array.
     *
     * @var string
     */
    protected $idField;

    /**
     * Name of the entity field used for the text parameter of the autocomplete results array.
     *
     * @var string
     */
    protected $textField;

    /**
     * Items per page for paginated results. Set to zero for unlimited.
     *
     * @var int
     */
    protected $ipp = 10;

    /**
     * If true, the text column will include the id column in parenthesis.
     *
     * @var bool
     */
    protected $concatenatedText = false;

    /**
     * EntityAutocomplete constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entity = $this->getEntityClass();
        $this->idField = $this->getIdField();
        $this->textField = $this->getTextField();
    }

    /**
     * @param string $queryString
     * @param int $page
     * @return array
     */
    public function getSuggestions(string $queryString, int $page = 1): array
    {
        $results = [
            'items' => [],
            'more'  => false,
        ];
        $qb = $this->entityManager->createQueryBuilder();
        $qb->from($this->entity, 'e')
            ->where("e.$this->idField = :key")
            ->orWhere("e.$this->textField LIKE :keyLike")
            ->setParameters(['key' => $queryString, 'keyLike' => $queryString . '%']);

        $this->alterQuery($qb);

        if ($this->ipp) {
            $count = clone $qb;
            $count->select("count(e.$this->idField)");
            $countResult = $count->getQuery()->getSingleResult();
            $qb->setFirstResult($this->ipp * ($page - 1))->setMaxResults($this->ipp);
        }

        $text = $this->concatenatedText ?
            "CONCAT(e.$this->textField, ' (', e.$this->idField, ')')" : "e.$this->textField";
        $qb->select(["e.$this->idField as id", "$text as text"]);
        $results['items'] = $qb->getQuery()->getResult();

        if ($this->ipp) {
            $results['more'] = ($this->ipp * $page) < reset($countResult);
        }

        return $results;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getLabels(array $ids): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $text = $this->concatenatedText ?
            "CONCAT(e.$this->textField, ' (', e.$this->idField, ')')" : "e.$this->textField";
        $qb->select(
            ["e.$this->idField as id", "$text as text"]
        )->from($this->entity, 'e')
            ->where($qb->expr()->in("e.$this->idField", ':id'))->setParameter('id', $ids);

        $this->alterQuery($qb);

        $results = $qb->getQuery()->getResult();

        return $results;
    }

    /**
     * Allows extending classes alter the query and add extra joins or wheres.
     *
     * @param QueryBuilder $qb
     */
    protected function alterQuery(QueryBuilder &$qb)
    {
    }

    /**
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * @return string
     */
    abstract protected function getIdField(): string;

    /**
     * @return string
     */
    abstract protected function getTextField(): string;
}
