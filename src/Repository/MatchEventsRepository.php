<?php

namespace App\Repository;

use App\Entity\MatchEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MatchEvents>
 *
 * @method MatchEvents|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatchEvents|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatchEvents[]    findAll()
 * @method MatchEvents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchEventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchEvents::class);
    }

    public function add(MatchEvents $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MatchEvents $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addMatchEvent($data): bool
    {
        if(empty($data) || empty($data['matchId']) || empty($data['teamId']) || empty($data['playerId']) || empty($data['eventId'])){
            return false;
        }

        $event = new MatchEvents();
        $event->setMatchId($data['matchId']);
        $event->setTeamId($data['teamId']);
        $event->setPlayer($data['playerId']);
        $event->setEventId($data['eventId']);

        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();

        return true;

    }

//    /**
//     * @return MatchEvents[] Returns an array of MatchEvents objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MatchEvents
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
