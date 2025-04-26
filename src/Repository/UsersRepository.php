<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Users>
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function saveAndFlush(Users $users): Users
    {
	    $this->getEntityManager()->persist($users);
	    $this->getEntityManager()->flush();
		$this->getEntityManager()->clear();

		return $users;
    }

	public function getUserBalance(int $userId): string
	{
		return $this->createQueryBuilder('u', 'u.balance')
			->select('u.balance')
			->andWhere('u.id = :id')
			->setParameter('id', $userId)
			->getQuery()
			->getSingleScalarResult();
	}
}
