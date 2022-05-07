<?php

namespace Jobs;

use Models\Db\Data;
use Services\Telegram as TelegramService;

class Telegram extends Job
{
    public function __construct()
    {
        $this->telegramService = new TelegramService();
        $this->dataModel = new Data();
    }

    public function run()
    {
        if (!$this->canRun('10:00')) {
            return;
        }
        $this->membersCountOfDolphinAnty();
        $this->membersCountOfDolphin();
        $this->membersCountOfOctoBrowser();
        $this->membersCountOfFbtool();
        $this->membersCountOfDolphinAntyNews();
        $this->membersCountOfDolphinNews();
        $this->membersCountOfIndigoBrowserNews();
        $this->membersCountOfOctoBrowserNews();
    }

    private function membersCountOfDolphinAnty()
    {
        $membersCount = $this->telegramService->membersCount('dolphin_anty');
        $this->dataModel->insert('telegram_members_chat_dolphin_anty', $membersCount);
    }

    private function membersCountOfDolphinAntyNews()
    {
        $membersCount = $this->telegramService->membersCount('dolphin_anty_news');
        $this->dataModel->insert('telegram_members_new_dolphin_anty', $membersCount);
    }

    private function membersCountOfDolphin()
    {
        $membersCount = $this->telegramService->membersCount('dolphin_affiliate');
        $this->dataModel->insert('telegram_members_chat_dolphin', $membersCount);
    }

    private function membersCountOfDolphinNews()
    {
        $membersCount = $this->telegramService->membersCount('dolphin_news');
        $this->dataModel->insert('telegram_members_news_dolphin', $membersCount);
    }

    private function membersCountOfOctoBrowser()
    {
        $membersCount = $this->telegramService->membersCount('octobrowser_chat');
        $this->dataModel->insert('telegram_members_chat_octo_browser', $membersCount);
    }

    private function membersCountOfIndigoBrowserNews()
    {
        $membersCount = $this->telegramService->membersCount('indigobrowser');
        $this->dataModel->insert('telegram_members_new_indigo_browser', $membersCount);
    }

    private function membersCountOfOctoBrowserNews()
    {
        $membersCount = $this->telegramService->membersCount('octobrowser');
        $this->dataModel->insert('telegram_members_news_octo_browser', $membersCount);
    }

    private function membersCountOfFbtool()
    {
        $membersCount = $this->telegramService->membersCount('fbtoolpro');
        $this->dataModel->insert('telegram_members_chat_fbtool', $membersCount);
    }
}
