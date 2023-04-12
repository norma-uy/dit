<?php

namespace App\Controller\Admin\Factory;

use App\Controller\Admin\Contracts\Orm\EntityPaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;

final class PaginatorFactory
{
    private AdminContextProvider $adminContextProvider;
    private EntityPaginatorInterface $entityPaginator;

    public function __construct(AdminContextProvider $adminContextProvider, EntityPaginatorInterface $entityPaginator)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->entityPaginator = $entityPaginator;
    }

    public function create(QueryBuilder $queryBuilder): EntityPaginatorInterface
    {
        $adminContext = $this->adminContextProvider->getContext();
        $paginatorDto = $adminContext->getCrud()->getPaginator();
        $paginatorDto->setPageNumber((int) $adminContext->getRequest()->query->get('page', '1'));

        return $this->entityPaginator->paginate($paginatorDto, $queryBuilder);
    }
}
