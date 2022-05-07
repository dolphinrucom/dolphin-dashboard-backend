<?php

namespace Jobs;

use Models\Db\Data;
use Models\Jira\Issue;
use Models\Jira\Project;
use Services\Jira as JiraService;

class Jira extends Job
{
    /**
     * @var JiraService
     */
    private $jiraService;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    /**
     * @var Project
     */
    private $projectModel;

    public function __construct()
    {
        $this->jiraService = new JiraService();
        $this->projectModel = new Project();
        $this->issueModel = new Issue();
        $this->dataModel = new Data();
    }

    public function run()
    {
        $this->antySupportOpenedIssues();
        $this->dolphinServerSupportOpenedIssues();
        $this->setRevenueForDolphinSupportIssues();
    }

    private function antySupportOpenedIssues()
    {
        if (!$this->canRun('15:00')) {
            return;
        }

        $jql = 'project = AS2 AND status in (Backlog, "In Progress", "To Do")';

        $issuesCount = $this->issueModel->countAll($jql);
        $this->dataModel->insert('anty_support_issues_count', $issuesCount);
    }

    private function dolphinServerSupportOpenedIssues()
    {
        if (!$this->canRun('15:00')) {
            return;
        }

        $jql = 'project = DSS AND status in ("In Progress", "To Do") AND type IN (Bug, "Referral request")';

        $issuesCount = $this->issueModel->countAll($jql);
        $this->dataModel->insert('dolphin_server_support_issues_count', $issuesCount);
    }

    private function setRevenueForDolphinSupportIssues()
    {
        if (!$this->every10Mins()) {
            return;
        }
        
        $jql = 'project = DSS AND status in ("To Do") AND type IN (Bug)';
        $issues = $this->issueModel->all($jql);

        $this->jiraService->assignRevenueForDolphinServerIssues($issues);
    }
}
