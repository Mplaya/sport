<?php

namespace App\Repository;

use App\Entity\Matches;
use App\Entity\MatchEvents;
use ContainerPkxjCAq\getMatchEventsRepositoryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matches>
 *
 * @method Matches|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matches|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matches[]    findAll()
 * @method Matches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchesRepository extends ServiceEntityRepository
{
    const HOME_TEAM_GOAL = 1;
    const AWAY_TEAM_GOAL = 50;

    const NOT_STARTED = 0;
    const MATCH_STARTED = 1;
    const FINISHED = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matches::class);
    }

    public function add(Matches $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Matches $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkMatchesExists($ht,$at): bool
    {
        $return = $this->createQueryBuilder('m')
            ->where('m.home_team_id = :ht')
            ->andWhere('m.away_team_id = :at')
            ->andWhere('m.status = 0')
            ->setParameter('ht', $ht)
            ->setParameter('at', $at)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if(!empty($return)){
            return true;
        }else{
            return false;
        }
    }

    public function findAllActiveMatches(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = 1')
            ->orderBy('m.match_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllFinishedMatches(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = 2')
            ->orderBy('m.home_team_score + m.away_team_score', 'DESC')
            ->addOrderBy('m.match_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllPendingMatches(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = 0')
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function startMatch($id): bool
    {
        try{
            $match = $this->find($id);
            $match->setMatchDate(new \DateTime());
            $match->setStatus(self::MATCH_STARTED);
            $this->getEntityManager()->persist($match);
            $this->getEntityManager()->flush();

            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function matchRandomEvent($id): bool
    {
        $match = $this->find($id);
        $todayTime = new \DateTime();
        $update = new \DateTime($match->getUpdatedAt()->format('Y-m-d H:i:s'));
        if($todayTime->getTimestamp() - $update->getTimestamp() < 10){
            return false;
        }

        $random = rand(1,100);
        $randPlayer = rand(2,10);

        if($random == self::HOME_TEAM_GOAL){
            $match->setHomeTeamScore($match->getHomeTeamScore()+1);
            $this->getEntityManager()->persist($match);
            $this->getEntityManager()->flush();


            $eventDetails = [
                'matchId' => $id,
                'teamId' => $match->getHomeTeamId(),
                'playerId' => $randPlayer,
                'eventId' => self::HOME_TEAM_GOAL
            ];

            $matchEvent = $this->getEntityManager()->getRepository(MatchEvents::class);
            $matchEvent->addMatchEvent($eventDetails);

            return true;
        }
        if($random == self::AWAY_TEAM_GOAL){
            $match->setAwayTeamScore($match->getAwayTeamScore()+1);
            $this->getEntityManager()->persist($match);
            $this->getEntityManager()->flush();


            $eventDetails = [
                'matchId' => $id,
                'teamId' => $match->getAwayTeamId(),
                'playerId' => $randPlayer,
                'eventId' => self::AWAY_TEAM_GOAL
            ];

            $matchEvent = $this->getEntityManager()->getRepository(MatchEvents::class);
            $matchEvent->addMatchEvent($eventDetails);

            return true;
        }
        return false;
    }

    public function endMatch($id): bool
    {
        $match = $this->find($id);
        $match->setStatus(self::FINISHED);
        $this->getEntityManager()->persist($match);
        $this->getEntityManager()->flush();

        return true;
    }

//    /**
//     * @return Matches[] Returns an array of Matches objects
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

//    public function findOneBySomeField($value): ?Matches
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
