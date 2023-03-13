<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Hashes the password when a user has been created or updated.
 */
class AdminUserEvent implements EventSubscriberInterface
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasherInterface)
    {
        $this->hasher = $hasherInterface;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPasswordOnCreatedUser'],
            BeforeEntityUpdatedEvent::class => ['hashPasswordOnUpdatedUser'],
        ];
    }

    public function hashPasswordOnCreatedUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof User)) {
            return;
        }

        $stringPassword = $entity->getPassword();
        $hashedPassword = $this->hasher->hashPassword($entity, $stringPassword);

        $entity->setPassword($hashedPassword);
        return;
    }

    public function hashPasswordOnUpdatedUser(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof User)) {
            return;
        }

        $stringPassword = $entity->getPassword();
        $hashedPassword = $this->hasher->hashPassword($entity, $stringPassword);

        $entity->setPassword($hashedPassword);
        return;
    }

}