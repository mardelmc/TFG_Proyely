<?php


namespace App\Repository;

use App\Entity\Student;
use App\Entity\StudentProjectPriority;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentProjectPriority|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentProjectPriority|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentProjectPriority[]    findAll()
 * @method StudentProjectPriority[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentProjectPriorityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentProjectPriority::class);
    }

    /**
     * Encuentra prioridades para un estudiante dado.
     *
     * @param Student $student
     * @return StudentProjectPriority[]
     */
    public function findByStudent(Student $student): array
    {
        return $this->createQueryBuilder('spp')
            ->andWhere('spp.student = :student')
            ->setParameter('student', $student)
            ->getQuery()
            ->getResult();
    }

    /**
     * Guarda o actualiza una instancia de StudentProjectPriority.
     *
     * @param StudentProjectPriority $priority
     */
    public function save(StudentProjectPriority $priority): void
    {
        $this->_em->persist($priority);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }


    /**
     * Elimina una instancia de StudentProjectPriority.
     *
     * @param StudentProjectPriority $priority
     * @param bool $flush
     */
    public function delete(StudentProjectPriority $priority, bool $flush = false): void
    {
        $this->_em->remove($priority);

        if ($flush) {
            $this->_em->flush();
        }
    }
}
