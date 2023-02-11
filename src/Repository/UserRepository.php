<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public_html function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public_html function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public_html function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public_html function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public_html function findCode($code)
    {
        return $this->createQueryBuilder('user')
            ->where('user.code LIKE :code AND user.isVerified = 0')
            ->setParameter('code', '%'.$code.'%')
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public_html function setConfirm($code) {
        return $this->createQueryBuilder('user')
            ->update()
            ->set('user.isVerified', ':true')
            ->where('user.code LIKE :code AND user.isVerified = 0')
            ->setParameter('code', $code)
            ->setParameter('true', '1')
            ->getQuery()
            ->execute()
        ;
    }
    public_html function isVerified($email) {
        return $this->createQueryBuilder('user')
            ->where('user.email LIKE :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleResult()
        ;
    }
//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public_html function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public_html function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
