<?php

namespace Jobs;

use Models\Db\Data;
use Services\Git as GitService;

class Git extends Job
{
    public function __construct()
    {
        $this->gitService = new GitService();
        $this->dataModel = new Data();
    }

    public function run()
    {
        $this->countAntyLines();
        $this->countDocsChars();
    }

    private function countAntyLines()
    {
        if (!$this->canRun('18:00')) {
            return;
        }

        $backendLines = $this->gitService->countLines($_ENV['GIT_REPO_ANTY_BACKEND']);
        $frontendLines = $this->gitService->countLines($_ENV['GIT_REPO_ANTY_FRONTEND']);
        $electronLines = $this->gitService->countLines($_ENV['GIT_REPO_ANTY_ELECTRON']);
        $localApiLines = $this->gitService->countLines($_ENV['GIT_REPO_ANTY_LOCAL_API']);
        $totalLines = $backendLines + $frontendLines + $electronLines + $localApiLines;

        $this->dataModel->insert('lines_of_code_anty_backend', $backendLines);
        $this->dataModel->insert('lines_of_code_anty_frontend', $frontendLines);
        $this->dataModel->insert('lines_of_code_anty_local_api', $localApiLines);
        $this->dataModel->insert('lines_of_code_anty_electron', $electronLines);
        $this->dataModel->insert('lines_of_code_anty_total', $totalLines);
    }

    private function countDocsChars()
    {
        if (!$this->canRun('18:00')) {
            return;
        }

        $charsCount = $this->gitService->countChars($_ENV['GIT_REPO_DOCS'], 'pages', '.md');
        $this->dataModel->insert('chars_count_docs_total', $charsCount);
    }
}
