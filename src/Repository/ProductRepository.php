<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function totalInCategory(Category $category): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p) as total')
            ->join('p.categories', 'c')
            ->andWhere('c.id = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult()['total'];
    }

    /**
     * @param string $search
     * @return int|mixed|string
     */
    public function search(string $search)
    {
        return $this->createQueryBuilder('p')
            ->join(Category::class, 'c', 'WITH', 'p.categories = c.id')
            ->select('p')
            ->orWhere('p.title LIKE :search')
            ->orWhere('p.text LIKE :search')
            ->orWhere('p.price LIKE :search')
            ->orWhere('p.createdAt LIKE :search')
            ->orWhere('c.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(Category $category, int $page = 0): array
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->join('p.categories', 'c')
            ->andWhere('c.id = :category')
            ->setParameter('category', $category)
            ->setFirstResult($_ENV['PAGE_LIMIT'] * $page)
            ->setMaxResults($_ENV['PAGE_LIMIT'])
            ->getQuery()
            ->getResult();
    }

}
