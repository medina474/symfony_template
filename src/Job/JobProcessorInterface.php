<?php

namespace App\Job;

use App\Entity\Job;

interface JobProcessorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(Job $job, ?string $data): array;
}
