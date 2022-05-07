<?php

namespace Services;

use GuzzleHttp\Client;
use Models\Db\Data;
use Models\Db\DolphinBackOffice;
use Models\Db\Slr;
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

    public function calculateSlrPerIssue($issues)
    {
        $this->slrModel = new Slr();

        foreach ($issues as $issue) {
            if ($issue->fields->status->name !== 'Done') {
                continue;
            }

            $start = strtotime($issue->fields->created);
            $end = $issue->fields->status->name === 'Done' ? strtotime($issue->fields->updated) : time();
            $diff = $end - $start;
            if ($diff < 60 * 10) {
                continue;
            }
            $diffHours = round($diff / 60 / 60);

            $this->slrModel->insert(
                explode('-', $issue->key)[0],
                $issue->key,
                date('Y-m-d', strtotime($issue->fields->created)),
                $diff,
                $diffHours
            );
        }
    }

    public function calculateSlrPerDay($project)
    {
        $this->slrModel = new Slr();
        $this->dataModel = new Data();

        $issues = $this->slrModel->perProject($project);

        $data = [];
        $source = $project . '_slr';

        foreach ($issues as $issue) {
            $week = date('W', strtotime($issue['date_created']));
            if (!isset($data[$week])) {
                $data[$week] = [
                    'total' => 0,
                    'green' => 0,
                    'red'   => 0
                ];
            }

            $data[$week]['total']++;
            if ($issue['hours'] <= $_ENV['SLR_THRESHOLD_HOURS']) {
                $data[$week]['green']++;
            } else {
                $data[$week]['red']++;
            }
        }

        $this->dataModel->deleteBySource($source);

        foreach ($data as $weekNumber => $values) {
            $data[$weekNumber]['slr'] = round(
                ($values['green'] / $values['total']) * 100
            );

            $this->dataModel->insert(
                $source,
                $data[$weekNumber]['slr'],
                date('Y-m-d', strtotime(date('Y') . 'W' . $weekNumber)),
                date('Y-m-d H:i:s', strtotime(date('Y') . 'W' . $weekNumber))
            );
        }
    }
}
