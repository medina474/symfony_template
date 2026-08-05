<?php

namespace App\Job;

use App\Entity\Job;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.job_processor')]
#[AsTaggedItem(index: self::class)]
final class JobFake implements JobProcessorInterface
{
    public function __construct(
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Job $job, ?string $data): array
    {
        $payload = $job->getPayload();
        
        if (($payload['behavior'] ?? null) === 'fail') {
            throw new \RuntimeException($payload['errorMessage'] ?? 'Échec simulé.');
        }

        if (($payload['behavior'] ?? null) === 'slow') {
            usleep(($payload['delayMs'] ?? 100) * 1000);
        }

        return $payload['expectedResult'] ?? ['fake' => true];
    }
}
