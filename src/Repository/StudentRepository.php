<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function add(Student $student): void
    {
        $this->getEntityManager()->persist($student);
    }
    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
    public function remove(Student $student): void
    {
        $this->getEntityManager()->remove($student);
    }

    public function findByTutor($tutor): array
    {
        return $this->createQueryBuilder('s')
        ->innerJoin('s.group', 'g')
        ->innerJoin('g.tutors', 't')
        ->where('t = :tutor')
        ->setParameter('tutor', $tutor)
        ->getQuery()
        ->getResult();
    }

    public function findStudentsWithFilters($academicYear, $studentName, $group): \Doctrine\ORM\Query
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.group', 'g')
            ->leftJoin('g.academicYear', 'ay')
            ->addSelect('g', 'ay');

        if ($academicYear) {
            $qb->andWhere('ay.id = :academicYear')
                ->setParameter('academicYear', $academicYear);
        }

        if ($studentName) {
            $qb->andWhere('CONCAT(s.firstName, \' \', s.lastName) LIKE :studentName')
                ->setParameter('studentName', '%' . $studentName . '%');
        }

        if ($group) {
            $qb->andWhere('g.id = :group')
                ->setParameter('group', $group);
        }

        return $qb->getQuery();
    }

//    /**
//     * @return Student[] Returns an array of Student objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Student
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
