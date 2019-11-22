<?php

namespace App\Repository;

use App\Entity\Sliders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Sliders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sliders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sliders[]    findAll()
 * @method Sliders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlidersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sliders::class);
    }

//    /**
//     * @return Sliders[] Returns an array of Sliders objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sliders
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
