<?php

namespace Jobs;

use Models\Db\Data;

class Anty extends Job
{
    public function __construct()
    {
        $this->antyModel = new \Models\Db\Anty();
        $this->dataModel = new Data();
    }

    public function run()
    {
        $this->refRegsByDay();
    }

    public function refRegsByDay()
    {
//        if ($this->canRun('04:00')) {
//            return;
//        }

        $this->dataModel->deleteBySource('anty_ref_regs_by_day');

        $regs = $this->antyModel->refRegsByDay();

        foreach ($regs as $reg) {
            $this->dataModel->insert(
                'anty_ref_regs_by_day',
                $reg['count(*)'],
                $reg['date']
            );
        }
    }
}
