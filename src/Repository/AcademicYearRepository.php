<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AcademicYear>
 *
 * @method AcademicYear|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcademicYear|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcademicYear[]    findAll()
 * @method AcademicYear[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademicYearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AcademicYear::class);
    }

    public function add(AcademicYear $academicYear): void
    {
        $this->getEntityManager()->persist($academicYear);
        $this->getEntityManager()->flush();
    }
    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
    public function remove(AcademicYear $academicYear): void
    {
        $this->getEntityManager()->remove($academicYear);
    }


    public function findAcademicYearsWithFilters($description): \Doctrine\ORM\Query
    {
        $qb = $this->createQueryBuilder('ay');

        if ($description) {
            $qb->andWhere('ay.description LIKE :description')
                ->setParameter('description', '%' . $description . '%');
        }

        return $qb->getQuery();
    }


//    /**
//     * @return AcademicYear[] Returns an array of AcademicYear objects
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

//    public function findOneBySomeField($value): ?AcademicYear
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
