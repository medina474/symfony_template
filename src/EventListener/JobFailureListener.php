<?php

namespace App\EventListener;

use App\Message\JobMessage;
use App\Notifier\AdminNotifierInterface;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

#[AsEventListener]
final class JobFailureListener
{
    public function __construct(
        private readonly JobRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly AdminNotifierInterface $notifier,
    ) {
    }

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $message = $event->getEnvelope()->getMessage();

        if (!$message instanceof JobMessage) {
            return;
        }

        $job = $this->repository->find($message->getId());
        if ($job === null) {
            return;
        }

        // Le stamp présent sur l'enveloppe reflète les tentatives *précédentes*,
        // pas celle qui vient d'échouer à l'instant : +1 pour compter l'échec courant.
        $previousRetries = $event->getEnvelope()->last(RedeliveryStamp::class)?->getRetryCount() ?? 0;
        $job->setRetryCount($previousRetries + 1);

        if (!$event->willRetry()) {
            $this->notifier->notifyJobFailure($job, $event->getThrowable());
        }

        $this->em->flush();
    }
}
