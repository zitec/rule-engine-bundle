<?php

namespace Zitec\RuleEngineBundle\Autocomplete;

/**
 * Class AutocompleteManager
 * A service that uses autocomplete data sources populated in a compiler pass to retrieve autocomplete results.
 */
class AutocompleteManager
{

    /**
     * @var AutocompleteInterface[]
     */
    protected $dataSources;

    /**
     * AutocompleteManager constructor.
     *
     * @param AutocompleteInterface[] $dataSources
     */
    public function __construct(array $dataSources = [])
    {
        $this->dataSources = $dataSources;
    }

    /**
     * @param mixed $key
     *  The key declared by the data source service.
     * @param mixed $queryString
     * @param int $page
     * @throws \Exception
     *
     * @return array
     */
    public function getSuggestions($key, $queryString, $page = 1)
    {
        if (!isset($this->dataSources[$key])) {
            throw new \Exception('No data source found for the given key.');
        }

        return $this->dataSources[$key]->getSuggestions($queryString, $page);
    }

    /**
     * @param mixed $key
     *  The key declared by the data source service.
     * @param array $ids
     * @return array
     * @throws \Exception
     */
    public function getLabels($key, array $ids)
    {
        if (!isset($this->dataSources[$key])) {
            throw new \Exception('No data source found for the given key.');
        }

        return $this->dataSources[$key]->getLabels($ids);
    }
}
