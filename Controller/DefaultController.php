<?php

namespace Zitec\RuleEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * Action for the autocomplete call used in the Autocomplete widget type in the RuleBuilder javascript component.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function autocompleteAction(Request $request)
    {
        $dataSourceKey = $request->get('key');
        $queryMode = $request->get('mode');
        $queryString = $request->get('q');

        switch ($queryMode) {
            case 'like':
                $page = $request->get('page');
                $results = $this->get('rule_engine.autocomplete')
                    ->getSuggestions($dataSourceKey, $queryString, $page);
                break;
            case 'label':
                $results = $this->get('rule_engine.autocomplete')
                    ->getLabels($dataSourceKey, $queryString);
                break;
            default:
                throw new \Exception('Invalid query mode.');
        }

        return new JsonResponse($results);
    }
}
