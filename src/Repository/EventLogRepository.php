<?php

namespace App\Repository;

use App\Entity\EventLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventLog>
 */
class EventLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventLog::class);
    }

    public function save(EventLog $eventLog, bool $flush = true): void
    {
        $this->getEntityManager()->persist($eventLog);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
