<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function showAllBooksByAuthor($id)

    {

        return $this->createQueryBuilder('b')

            ->join('b.author','a')

            ->addSelect('a')

            ->where('a.id = :id')

            ->setParameter('id', $id)

            ->getQuery()->getResult() ;}
    public function findref($ref)

    {

        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();}
    public function findAllByAuthor()
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findAllByDateAndNbBook()
    {
        $year2023 = new \DateTime('2023-01-01');

        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->where('b.publishedDate < :year2023')
            ->setParameter('year2023', $year2023)
            ->andWhere('a.nb_books > 35')
            ->getQuery()
            ->getResult();

    }
    public function updateCategory()
    {

        $entityManager = $this->getEntityManager();

        $dql = 'UPDATE App\Entity\Book b
                SET b.category = :newCategory
                WHERE b.author IN (
                    SELECT a.id
                    FROM App\Entity\Author a
                    WHERE a.username = :authorName
                )';

        $query = $entityManager->createQuery($dql);
        $query->setParameter('newCategory', 'Romance');
        $query->setParameter('authorName', 'William Shakespeare');

        return $query->execute();
    }
    public function sumBooksInCategory()
    {
        $entityManager = $this->getEntityManager();


        $query = $entityManager->createQuery('SELECT COUNT(b)
            FROM App\Entity\Book b
            WHERE b.category = :category');
        $query->setParameter('category', 'Science-Fiction');

        return $query->getSingleScalarResult();
    }
    public function findBooksBetween2Dates()
    {
        $entityManager = $this->getEntityManager();
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2018-12-31');
        $dql = 'SELECT b
                FROM App\Entity\Book b
                WHERE b.publishedDate BETWEEN :startDate AND :endDate';

        $query = $entityManager->createQuery($dql);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);

        return $query->getResult();
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
