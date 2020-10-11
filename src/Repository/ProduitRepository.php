<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }



    public function findAllOrderBy($value)
    {

        return $this->createQueryBuilder('p')
            ->andWhere('p.societe = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();

    }

    // méthode permettant de récupérer la donnée 'nom' de la table produit en bdd
    public function search(string $filter)
    {
        $builder = $this->createQueryBuilder('prod');

        $builder
            ->andWhere('prod.nom LIKE :nom')
            ->setParameter('nom', '%'. $filter . '%')
        ;

        $query = $builder->getQuery();

        return $query->getResult();

    }

    // méthode liée à l'autocomplétion de la barre de recherche

    public function autocomplete($term)
    {
        $qb = $this->createQueryBuilder('produit');

        $qb->select('produit.nom')
            ->where('produit.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%');

        $arrayAss = $qb->getQuery()
            ->getResult();

        $array = array();

        // le résultat de la requête est bouclé afin d'effectuer la recherche sur chaque ligne de la table produit
        foreach ($arrayAss as $data) {

            $array[] = $data['nom'];
        }

        return $array;
    }
    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
