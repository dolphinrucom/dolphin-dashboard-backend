<?php

namespace Models;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;

class RestApi
{
    public function parseJsonResponse($res): object
    {
        $contents = $res->getBody()->getContents();
        
        if ((bool)$_ENV['DEBUG']) {
            echo 'parseJsonResponse: Got data to parse:' . PHP_EOL;
            dump($contents);
        }

        return json_decode($contents);
    }
}
