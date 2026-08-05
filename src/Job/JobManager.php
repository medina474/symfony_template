<?php

namespace App\Job;

use App\Entity\Job;
use App\Message\JobMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class JobManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @param class-string<JobProcessorInterface> $processorClass
     * @param array<string, mixed> $payload
     */
    public function dispatch(
        string $processorClass, 
        array $payload = [], 
        ?string $data = null
    ): Job
    {
        // Because the type is coming from a PHPDoc, you can turn off this check
        /*
        if (!is_a($processorClass, JobProcessorInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'La classe "%s" doit implémenter %s.',
                $processorClass,
                JobProcessorInterface::class,
            ));
        }
        */

        $job = new Job(
            action: $processorClass,
            payload: $payload
        );

        $this->entityManager->persist($job);
        $this->entityManager->flush();

        $this->messageBus->dispatch(
            new JobMessage($job->getId(), $payload, $data)
        );
    
        return $job;
    }
}
