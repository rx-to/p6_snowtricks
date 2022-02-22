<?php

namespace App\Repository;

use App\Entity\TrickMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrickMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickMessage[]    findAll()
 * @method TrickMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickMessage::class);
    }

    public function countPages(int $trickID, int $limit): int
    {
        $conn       = $this->getEntityManager()->getConnection();
        $sql        = "SELECT COUNT(*) `countMessages` FROM `trick_message` WHERE `trick_id` = :trick_id";
        $stmt       = $conn->prepare($sql);
        $result     = $stmt->executeQuery([':trick_id' => $trickID]);
        $countPages = intval(($result->fetchAssociative()['countMessages'] - 1) / $limit) + 1;

        return $countPages;
    }

    // /**
    //  * @return TrickMessage[] Returns an array of TrickMessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrickMessage
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
