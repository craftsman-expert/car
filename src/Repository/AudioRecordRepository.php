<?php

namespace App\Repository;

use App\Entity\AudioRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AudioRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method AudioRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method AudioRecord[]    findAll()
 * @method AudioRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudioRecordRepository extends ServiceEntityRepository
{
    /**
     * AudioRecordRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AudioRecord::class);
    }


    /**
     * @param $name
     * @param string $performer
     * @param int $offset
     * @param int $count
     * @return int|mixed|string
     */
    public function search($name = '', $performer = '', $offset = 0, $count = 100)
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($name)) {
            $qb = $qb->orWhere('a.name like :name')->setParameter('name',"%$name%");
        }

        if (!empty($performer)) {
            $qb = $qb->leftJoin("a.performer", "performer");
            $qb = $qb->orWhere('performer.name like :performer')->setParameter('performer',"%$performer%");
        }

        return $qb
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

}
