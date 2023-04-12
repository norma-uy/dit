<?php

namespace App\Repository;

use App\Entity\MediaCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaCategory>
 *
 * @method MediaCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaCategory[]    findAll()
 * @method MediaCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaCategory::class);
    }

    public function save(MediaCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MediaCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneBySlug(string $slug, ?MediaCategory $mediaException = null): ?MediaCategory
    {
        $qb = $this->createQueryBuilder('mc')
            ->andWhere('mc.slug = :slug')
            ->setParameter('slug', $slug);

        if ($mediaException !== null && $mediaException->getId()) {
            $qb->andWhere('mc.id <> :mediaException')->setParameter('mediaException', $mediaException->getId());
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    //    /**
    //     * @return MediaCategory[] Returns an array of MediaCategory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MediaCategory
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
