<?php

namespace App\Notifier;

use App\Entity\Job;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final class JobFailureAdminNotifier implements AdminNotifierInterface
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly string $adminEmail,
        private readonly ?string $adminPhone = null,
    ) {
    }

    public function notifyJobFailure(Job $job, \Throwable $exception): void
    {
        $notification = (new Notification(
            sprintf('Échec définitif du job %s', $job->getAction())
        ))
            ->content(sprintf(
                "Job : %s\nAction : %s\nTentatives : %d\nErreur : %s",
                $job->getId(),
                $job->getAction(),
                $job->getRetryCount(),
                $exception->getMessage(),
            ))
            ->importance(Notification::IMPORTANCE_URGENT);

        $this->notifier->send($notification, new Recipient($this->adminEmail, $this->adminPhone));
    }
}
