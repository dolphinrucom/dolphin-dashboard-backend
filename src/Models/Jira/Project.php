<?php

namespace Models\Jira;

use ErrorException;
use GuzzleHttp\Exception\GuzzleException;
use Models\RestApi;

class Project extends RestApi
{
    public function __construct()
    {
        $this->jiraService = new \Services\Jira();
        $this->client = $this->jiraService->getClient();
    }

    public function all(): array
    {
        try {
            $res = $this->client->get('project/search');
        } catch (GuzzleException $e) {
            throw new ErrorException(
                'Could not fetch data from Jira. Message from Guzzle: ' . $e->getMessage()
            );
        }

        return $this->parseJsonResponse($res)->values;
    }
}
