<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
$this->logger = $logger;
        parent::__construct($registry, Project::class);
    }
    public function add(Project $project): void
    {
$this->logger->info('Saving project', ['project' => $project->getName()]);
        $this->getEntityManager()->persist($project);
    }
    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
    public function remove(Project $project): void
    {
        $this->getEntityManager()->remove($project);
    }


//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function listAll(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, s, t')
            ->leftJoin('p.student', 's')
            ->leftJoin('p.proposedBy', 't')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(?string $student, ?string $proposedBy): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p, s, t')
            ->leftJoin('p.student', 's')
            ->leftJoin('p.proposedBy', 't');

        if ($student) {
            $qb->andWhere('s.name LIKE :student')
                ->setParameter('student', '%' . $student . '%');
        }

        if ($proposedBy) {
            $qb->andWhere('t.name LIKE :proposedBy')
                ->setParameter('proposedBy', '%' . $proposedBy . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
