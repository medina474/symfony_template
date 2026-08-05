<?php

namespace App\Notifier;

use App\Entity\Job;

interface AdminNotifierInterface
{
    public function notifyJobFailure(Job $job, \Throwable $exception): void;
}
