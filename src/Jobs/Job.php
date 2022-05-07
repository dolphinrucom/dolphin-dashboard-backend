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
}
