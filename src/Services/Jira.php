<?php

namespace Services;

use GuzzleHttp\Client;

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
}
