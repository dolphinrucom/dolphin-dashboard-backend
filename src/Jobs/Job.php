<?php

namespace Jobs;

class Job
{
    protected function canRun(string $time): bool
    {
        return date('H:i') === $time;
    }
}
