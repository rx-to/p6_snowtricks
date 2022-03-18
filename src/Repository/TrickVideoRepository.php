<?php

namespace App\Repository;

use App\Entity\TrickVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrickVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickVideo[]    findAll()
 * @method TrickVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickVideo::class);
    }
}
