<?php

namespace App\Repository;

use App\Entity\MediaCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaCollection>
 *
 * @method MediaCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaCollection[]    findAll()
 * @method MediaCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaCollection::class);
    }

    public function save(MediaCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MediaCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return MediaCollection[] Returns an array of MediaCollection objects
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

    public function findOneByAsHomeSlider(): ?MediaCollection
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.setAsHomeSlider = 1')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySlug(string $slug, ?MediaCollection $mediaColletionException = null): ?MediaCollection
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.slug = :slug')
            ->setParameter('slug', $slug);

        if ($mediaColletionException !== null && $mediaColletionException->getId()) {
            $qb->andWhere('m.id <> :mediaColletionException')->setParameter(
                'mediaColletionException',
                $mediaColletionException->getId(),
            );
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
