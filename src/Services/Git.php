<?php

namespace Services;

class Git
{
    public function countLines(string $repoUrl, $internalPath = null): int
    {
        $localRepoPath = APP_PATH . '/tmp/' . basename($repoUrl);
        `rm -rf $localRepoPath; mkdir -p $localRepoPath`;

        try {
            `git clone $repoUrl '$localRepoPath'`;

            $command = "cd '$localRepoPath'; git ls-files $internalPath | xargs cat | wc -l";
            $shellOutput = shell_exec($command);
        } finally {
            `rm -rf $localRepoPath`;
        }

        return (int)trim($shellOutput);
    }

    public function countChars(string $repoUrl, $internalPath = null, $filePattern = null): int
    {
        $localRepoPath = APP_PATH . '/tmp/' . basename($repoUrl);
        `rm -rf $localRepoPath; mkdir -p $localRepoPath`;

        try {
            `git clone $repoUrl '$localRepoPath'`;

            $command = "cd '$localRepoPath'; git ls-files $internalPath | grep '$filePattern' | xargs cat | wc -m";
            $shellOutput = shell_exec($command);
        } finally {
            `rm -rf $localRepoPath`;
        }

        return (int)trim($shellOutput);
    }
}
