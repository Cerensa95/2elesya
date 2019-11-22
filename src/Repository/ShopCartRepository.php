<?php

namespace App\Repository;

use App\Entity\ShopCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ShopCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopCart[]    findAll()
 * @method ShopCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopCartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ShopCart::class);
    }

//    /**
//     * @return ShopCart[] Returns an array of ShopCart objects
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

    // User shopcart products
    public function getUserShopcart($userid): array
    {
        $em = $this->getEntityManager();
        $query = $em ->createQuery(
            "SELECT p.price, s.quantity, s.productid,s.name, s.userid, p.price as total FROM App\Entity\ShopCart s, App\Entity\Sales p WHERE s.productid = p.id and s.userid = :userid"
        )->setParameter('userid', $userid);
        return $query->getResult();
    }

    // Sum of user shopcart products
    public function getUserShopCartTotal($userid): ?float
    {
        $em = $this-> getEntityManager();
        $query = $em -> createQuery('SELECT sum(p.price) as total FROM App\Entity\ShopCart s, App\Entity\Sales p WHERE s.productid = p.id and s.userid = :userid')->setParameter('userid',$userid);
        $result = $query->getResult();

        if($result[0]["total"] != null) {
            return $result[0]["total"];

        }else {
            return 0;
        }
    }
    
}
