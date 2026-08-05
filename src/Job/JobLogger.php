<?php

namespace App\Job;

use App\Entity\Job;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.job_processor')]
#[AsTaggedItem(index: self::class)]
final class JobLogger implements JobProcessorInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Job $job, ?string $data): array
    {
        $this->logger->error($data, $job->getPayload());
        return [ "message" => $data];
    }
}
