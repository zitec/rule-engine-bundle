<?php

namespace Zitec\RuleEngineBundle\Autocomplete;

/**
 * Interface AutocompleteInterface
 * Interface for an autocomplete data source service.
 */
interface AutocompleteInterface
{
    /**
     * Get autocomplete suggestions based on a search string.
     *
     * @param string $queryString
     *  The user search string.
     * @param int $page
     *  The page number for paginated results.
     *
     * @return array
     *  An and array consisting of a collection of items where each item is an object with id and text properties
     *      and a "more" flag indicating partial (paged) results.
     */
    public function getSuggestions(string $queryString, int $page = 1): array;

    /**
     * Retrieve the text parameter of existing ids to populate the front end autocomplete element.
     *
     * @param array $ids
     *
     * @return array
     *  An array of items where each item is an object with id and text properties.
     */
    public function getLabels(array $ids): array;
}
