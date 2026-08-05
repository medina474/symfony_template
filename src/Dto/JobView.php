<?php

namespace App\Dto;

use App\Entity\Job;

final readonly class JobView
{
    public function __construct(
        public string $id,
        public string $action,
    ) {
    }

    public static function fromEntity(Job $job): self
    {
        return new self(
            $job->getId()->toRfc4122(),
            $job->getAction(),
        );
    }
}
