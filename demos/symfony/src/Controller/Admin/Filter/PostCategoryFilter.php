<?php

namespace App\Controller\Admin\Filter;

use App\Form\Type\PostCategoryCollectionType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;

class PostCategoryFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(PostCategoryCollectionType::class);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        FilterDataDto $filterDataDto,
        ?FieldDto $fieldDto,
        EntityDto $entityDto,
    ): void {
        if (!empty($filterDataDto->getValue())) {
            $queryBuilder
                ->leftJoin(sprintf('%s.postCategories', $filterDataDto->getEntityAlias()), 'pc')
                ->andWhere('pc.id = :postCategoryId')
                ->setParameter('postCategoryId', $filterDataDto->getValue());
        }
    }
}
