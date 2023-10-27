<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }
public function findAll28(){
    $req= $this->createQueryBuilder('a')
->orderBy('a.email','ASC')
        ->getQuery();

       //afficher requette  dd($req->getSQL());
    //afficher entity   dd($req->getDQL());
    return $req->getResult();
}
public function findAll3(){
    $em=$this->getEntityManager();
    $req=$em->createQuery('SELECT a FROM App\Entity\Author a');
    return $req->getResult();
}

    public function findminmax($min,$max)

    {

        $qb = $this->createQueryBuilder('a');

        if ($min !== null) {
            $qb->andWhere($qb->expr()->gte('a.nb_books', ':min'));
            $qb->setParameter('min', $min);
        }

        if ($max !== null) {
            $qb->andWhere($qb->expr()->lte('a.nb_books', ':max'));
            $qb->setParameter('max', $max);
        }

        return $qb->getQuery()->getResult();}
    // src/Repository/AuthorRepository.php

// src/Repository/AuthorRepository.php

    public function findAuthorsWithZeroBooks()
    {
        return $this->createQueryBuilder('a')
            ->where('a.nb_books = 0')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
