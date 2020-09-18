<?php

namespace App\Repository;

use App\Entity\Performer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Performer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Performer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Performer[]    findAll()
 * @method Performer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerformerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Performer::class);
    }

    /**
     * @param string $name
     * @param int $offset
     * @param int $count
     * @return int|mixed|string
     */
    public function search($name = '', $offset = 0, $count = 100)
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($name)) {
            $qb = $qb->orWhere('p.name like :name')
                ->setParameter('name',"%$name%");
        }

        return $qb
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }
}
