<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function add(Group $group): void
    {
        $this->getEntityManager()->persist($group);
    }
    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
    public function remove(Group $group): void
    {
        $this->getEntityManager()->remove($group);
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findGroupsWithFilters($academicYear, $groupName): \Doctrine\ORM\Query
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.academicYear', 'ay')
            ->addSelect('ay');

        if ($academicYear) {
            $qb->andWhere('ay.id = :academicYear')
                ->setParameter('academicYear', $academicYear);
        }

        if ($groupName) {
            $qb->andWhere('g.name LIKE :groupName')
                ->setParameter('groupName', '%' . $groupName . '%');
        }

        return $qb->getQuery();
    }
    public function findGroupsByTutor(int $teacherId): array
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.tutors', 't')
            ->where('t.id = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Group[] Returns an array of Group objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Group
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
