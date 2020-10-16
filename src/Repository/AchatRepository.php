<?php

namespace App\Repository;

use App\Entity\Achat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
    }


    public function findbysociete($produitsoc)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.produit.societe = :produit_societe')
            ->setParameter('produit_societe', $produitsoc)
            ->orderBy('a.commande', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findbyproduit($produits)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.produit = :produit')
            ->setParameter('produit', $produits)
            ->orderBy('a.commande', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Achat[] Returns an array of Achat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Achat
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
