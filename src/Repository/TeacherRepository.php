<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 *
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    public function add(Teacher $teacher): void
    {
        $this->getEntityManager()->persist($teacher);
    }
    public function save(Teacher $teacher = null, bool $isNew = false): void
    {
        $em = $this->getEntityManager();

        if ($isNew) {
            $em->persist($teacher);
        }

        $em->flush();
    }

    public function remove(Teacher $teacher): void
    {
        $this->getEntityManager()->remove($teacher);
    }

    public function findTeachersWithFilters(?string $academicYear, ?string $teacherName, ?int $groupId): \Doctrine\ORM\Query
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->leftJoin('t.academicYears', 'ay')
            ->leftJoin('t.groups', 'g');

        if ($academicYear) {
            $queryBuilder->andWhere('ay.id = :academicYear')
                ->setParameter('academicYear', $academicYear);
        }

        if ($teacherName) {
            $queryBuilder->andWhere('CONCAT(t.firstName, \' \', t.lastName) LIKE :teacherName')
                ->setParameter('teacherName', '%' . $teacherName . '%');
        }

        if ($groupId) {
            $queryBuilder->andWhere('g.id = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $queryBuilder->getQuery();
    }

//    /**
//     * @return Teacher[] Returns an array of Teacher objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Teacher
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
