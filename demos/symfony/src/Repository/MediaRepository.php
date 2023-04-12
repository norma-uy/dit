<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function save(Media $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Media $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Media[] Returns an array of Media objects
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

    public function findOneBySlug(string $slug, ?Media $mediaException = null): ?Media
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.slug = :slug')
            ->setParameter('slug', $slug);

        if ($mediaException !== null && $mediaException->getId()) {
            $qb->andWhere('m.id <> :mediaException')->setParameter('mediaException', $mediaException->getId());
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findPerPage(int $page = 1, int $categoryId = null, string $searchTxt = null): array
    {
        $qb = $this->createQueryBuilder('m');

        $qb = $qb
            ->where(
                $qb
                    ->expr()
                    ->orX(
                        $qb->expr()->like('m.mimeType', $qb->expr()->literal('image/%')),
                        $qb->expr()->like('m.mimeType', $qb->expr()->literal('video/%')),
                    ),
            )
            ->orderBy('m.id', 'DESC');

        if ($categoryId) {
            $qb->innerJoin('m.mediaCategories', 'mc', 'WITH', 'mc.id = :categoryId')->setParameter(
                'categoryId',
                $categoryId,
            );
        }

        $query = $qb->getQuery();

        //set page size
        $pageSize = 48;

        // load doctrine Paginator
        $paginator = new Paginator($query);

        // you can get total items
        $totalItems = count($paginator);

        // get total pages
        $pagesCount = ceil($totalItems / $pageSize);

        $items = $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page > 0 ? $page - 1 : 0)) // set the offset
            ->setMaxResults($pageSize)
            ->getResult(); // set the limit

        return [
            'items' => $items,
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'currentPage' => $page,
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page <= $pagesCount ? $page + 1 : $pagesCount,
        ];
    }
}
