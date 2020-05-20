<?php

namespace App\Repository;

use App\Entity\Password;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Password|null find($id, $lockMode = null, $lockVersion = null)
 * @method Password|null findOneBy(array $criteria, array $orderBy = null)
 * @method Password[]    findAll()
 * @method Password[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordRepository extends ServiceEntityRepository
{
    /**
     * PasswordRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Password::class);
    }

    /**
     * @param $user_id
     * @param null $password
     * @return Password|null
     */
    public function findValidPassword($user_id, $password = null): ?Password
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.created >= :interval')
            ->andWhere('p.client = :val')
            ->setParameter('val', $user_id)
            ->setParameter('interval', new \DateTime('-2 minutes'))
        ;

        if ($password) {
            $qb->andWhere('p.hash = :password')
                ->setParameter('password', $password);
        }

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

}
