<?php

namespace App\Repository;

use App\Entity\Roadtrip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Roadtrip>
 */
class RoadtripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Roadtrip::class);
    }

    //    /**
    //     * @return Roadtrip[] Returns an array of Roadtrip objects
    //     */
       public function findByUsernameId($value): array
       {
           return $this->createQueryBuilder('r')
               ->andWhere('r.id = :val')
               ->setParameter('val', $value)
               ->orderBy('r.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?Roadtrip
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findLastRoadtrip($user) {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user_id = :user')
            ->setParameter('user', $user)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}


/**
 * RoadtripRepository.php on line 45:
    App\Entity\Roadtrip {#1332 ▼
    -id: 60
    -title: "Amet voluptas iure iste qui."
    -cover_image: "https://picsum.photos/600/400"
    -user_id: 
    App\Entity
    \
    User {#1020 ▶}
    -vehicle: 
    Proxies\__CG__\App\Entity
    \
    Vehicles {#1268 ▶}
    -created_at: DateTimeImmutable @1737366343 {#1330 ▶}
    -updated_at: DateTimeImmutable @1737366343 {#1331 ▶}
    +checkpoints: 
    Doctrine\ORM
    \
    PersistentCollection {#1333 ▶}
    -description: "Ut voluptatibus optio consequuntur saepe expedita quidem soluta autem. Maiores et facere assumenda dolores voluptatibus dolor. Cumque a illum ducimus."
    }
 */