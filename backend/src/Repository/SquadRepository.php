<?php
namespace App\Repository;

use App\Entity\Squad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Squad>
 *
 * @method Squad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Squad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Squad[]    findAll()
 * @method Squad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SquadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Squad::class);
    }

    public function save(Squad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Squad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Squad[] Returns an array of Squad objects associated with a user ID
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
