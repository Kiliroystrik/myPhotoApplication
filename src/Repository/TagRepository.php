<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    //    /**
    //     * @return Tag[] Returns an array of Tag objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tag
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getMostUsedTags(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id, t.name, count(p.id) as count')
            ->leftJoin('t.photos', 'p')
            ->groupBy('t.id')
            ->orderBy('count', 'DESC')
            ->setMaxResults(4);

        return $qb->getQuery()->getResult();
    }

    public function search(string $search): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(?string $name, ?int $id): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($name) {
            $qb
                ->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        if ($id) {
            $qb
                ->andWhere('t.id = :id')
                ->setParameter('id', $id);
        }

        $qb
            ->orderBy('t.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function save(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
