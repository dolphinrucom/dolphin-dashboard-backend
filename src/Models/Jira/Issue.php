<?php

namespace Models\Jira;

class Issue extends \Models\RestApi
{
    public function __construct()
    {
        $this->jiraService = new \Services\Jira();
        $this->jiraRestApiClient = $this->jiraService->getClient();
    }

    /**
     * @param string $jql
     *
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function countAll(string $jql)
    {
        $res = $this->jiraRestApiClient->get('search', [
            'query' => [
                'jql'        => $jql,
                'maxResults' => 1
            ]
        ]);

        return $this->parseJsonResponse($res)->total;
    }

    public function all(string $jql)
    {
        $res = $this->jiraRestApiClient->get('search', [
            'query' => [
                'jql'        => $jql,
                'maxResults' => 100
            ]
        ]);

        return $this->parseJsonResponse($res)->issues;
    }

    public function update(string $issueKey, array $data): void
    {
        $this->jiraRestApiClient->put(
            'issue/' . $issueKey,
            [
                'json' => $data
            ]
        );
    }
}
