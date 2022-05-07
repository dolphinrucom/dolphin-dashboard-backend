<?php

namespace Services;

class Telegram
{
    public function membersCount(string $channel): int
    {
        $html = file_get_contents("https://t.me/$channel");

        preg_match('/[\d ]+ (members|subscribers)/m', $html, $matches);

        if (!empty($matches)) {
            return (int)str_replace(' ', '', $matches[0]);
        }

        return 0;
    }
}
