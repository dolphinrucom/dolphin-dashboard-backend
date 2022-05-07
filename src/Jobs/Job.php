<?php

namespace Jobs;

class Job
{
    protected function canRun(string $time): bool
    {
        return date('H:i') === $time;
    }

    protected function every10Mins()
    {
        return in_array(
            date('m'),
            ['00', '10', '20', '30', '40', '50', '60']
        );
    }

    protected function every5Mins()
    {
        return in_array(
            date('m'),
            ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55', '60']
        );
    }
}
