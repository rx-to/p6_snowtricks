<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function countPages(int $limit): int
    {
        $conn       = $this->getEntityManager()->getConnection();
        $sql        = "SELECT COUNT(*) `countTricks` FROM `trick`";
        $stmt       = $conn->prepare($sql);
        $result     = $stmt->executeQuery([]);
        $countPages = intval(($result->fetchAssociative()['countTricks'] - 1) / $limit) + 1;

        return $countPages;
    }

    public function maxID(): int
    {
        $conn       = $this->getEntityManager()->getConnection();
        $sql        = "SELECT MAX(`id`) `maxID` FROM `trick`";
        $stmt       = $conn->prepare($sql);
        $result     = $stmt->executeQuery([]);
        $maxID = intval($result->fetchAssociative()['maxID']);

        return $maxID;
    }
}
