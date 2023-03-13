<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

class UserEventListener
{
    public function __invoke(User $user, LifecycleEventArgs $lifecycleEventArgs)
    {
       // You can emit events, it triggers when a user has been registered.
    }
}