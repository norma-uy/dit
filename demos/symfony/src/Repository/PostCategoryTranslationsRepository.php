<?php

namespace App\Repository;

use App\Entity\PostCategoryTranslations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostCategoryTranslations>
 *
 * @method PostCategoryTranslations|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostCategoryTranslations|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostCategoryTranslations[]    findAll()
 * @method PostCategoryTranslations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostCategoryTranslationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostCategoryTranslations::class);
    }

    public function save(PostCategoryTranslations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PostCategoryTranslations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return PostCategoryTranslations[] Returns an array of PostCategoryTranslations objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PostCategoryTranslations
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
