<?php

namespace App\Repository;

use App\Entity\User;
use App\Repository\Datatable\DatatableConfigColumn;
use App\Repository\Datatable\DatatableConfigJoin;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableRepository;
use Doctrine;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends DatatableRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $slug
     * @return User[]
     */
    public function findByProfession(string $slug): array
    {
        $qb = $this->createQueryBuilder('user');

        $qb
            ->join('user.profession', 'profession')
            ->where('profession.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $slug
     * @return string[]
     */
    public function findEmailsByProfession(string $slug): array
    {
        $qb = $this->createQueryBuilder('user');

        $qb
            ->select('user.email')
            ->join('user.profession', 'profession')
            ->where('profession.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        return $qb->getQuery()->getResult(Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }
    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    #[Pure] public function configureDatableJoin(): array
    {
        return [
            new DatatableConfigJoin('profession')
        ];
    }

    #[Pure] public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('lastName'),
            new DatatableConfigSearch('firstName'),
            new DatatableConfigSearch('email'),
            new DatatableConfigSearch('phoneNumber'),
            new DatatableConfigSearch('name', 'profession'),
        ];
    }

    #[Pure] public function configureDatableColumns(): array
    {
        return [
            new DatatableConfigColumn('lastName'),
        ];
    }
}
