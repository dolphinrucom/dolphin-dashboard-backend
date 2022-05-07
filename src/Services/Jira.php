<?php

namespace Services;

use GuzzleHttp\Client;
use Models\Db\DolphinBackOffice;
use Models\Jira\Issue;

class Jira
{
    public function getClient()
    {
        return new Client([
            'base_uri' => $this->getBaseUrl(),
            'headers'  => [
                'Authorization' => 'Basic ' . base64_encode($this->getBasicAuth()),
            ],
        ]);
    }

    private function getBasicAuth(): string
    {
        return "{$_ENV['JIRA_USER']}:{$_ENV['JIRA_API_TOKEN']}";
    }

    private function getBaseUrl(): string
    {
        $url = $_ENV['JIRA_BASE_URL'];

        if (substr($url, -1) !== '/') {
            $url = $url . '/';
        }

        return $url;
    }

    public function assignRevenueForDolphinServerIssues(array $issues)
    {
        $this->dolphinBackOfficeModel = new DolphinBackOffice();
        $this->issueModel = new Issue();

        foreach ($issues as $issue) {
            $revenueExistsInIssueSummary = preg_match(
                '/^\[[\d руб\.]+\]+/',
                $issue->fields->summary
            );

            if (!$revenueExistsInIssueSummary) {
                $revenue = $this->dolphinServerRevenueByIssue($issue);
                $dataToUpdate = [
                    'fields' => [
                        'summary' => '[' . number_format($revenue, 0, '', ' ') . ' руб.] ' . $issue->fields->summary
                    ]
                ];
                $this->issueModel->update($issue->key, $dataToUpdate);
            }
        }
    }

    /**
     * @param mixed $issue
     *
     * @return array[]
     */
    private function getDolphinServerLicenseMatches(object $issue): array
    {
        $matches = [
            'ip'    => [],
            'email' => [],
            'lic'   => []
        ];

        preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/m', $issue->fields->description, $matches['ip']);
        preg_match(
            '/([a-zA-Z0-9\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})/m',
            $issue->fields->description,
            $matches['email']
        );
        preg_match('/[a-z0-9]{32}/m', $issue->fields->description, $matches['lic']);

        return $matches;
    }

    private function dolphinServerRevenueByIssue(object $issue): int
    {
        $matches = $this->getDolphinServerLicenseMatches($issue);

        if ($matches['ip']) {
            $revenue = $this->dolphinBackOfficeModel->revenueByIp($matches['ip'][0]);
        } elseif ($matches['email']) {
            $revenue = $this->dolphinBackOfficeModel->revenueByEmail($matches['email'][0]);
        } elseif ($matches['lic']) {
            $revenue = $this->dolphinBackOfficeModel->revenueByLic($matches['lic'][0]);
        } else {
            $revenue = 0;
        }

        return $revenue;
    }
}
