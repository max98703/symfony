<?php

namespace Api\Repository;

use Doctrine\ORM\QueryBuilder;
use Api\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;


class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @param array $filters
     * @param string $select
     * @param string $alias
     * @return QueryBuilder
     *
     * This function will return the users data as per select statement and filters we use
     */
    public function findAllQuery(array $filters = [], string $select = 'u', string $alias = 'u'): QueryBuilder
    {

        return $this->_em->createQueryBuilder()
            ->select($select)
            ->from(User::class, $alias);
    }
}




