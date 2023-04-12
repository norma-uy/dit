<?php

namespace App\Controller\Admin\Filter;

use App\Form\Type\MediaCategoryCollectionType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;

class MediaCategoryFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(MediaCategoryCollectionType::class);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        FilterDataDto $filterDataDto,
        ?FieldDto $fieldDto,
        EntityDto $entityDto,
    ): void {
        if (!empty($filterDataDto->getValue())) {
            $queryBuilder
                ->leftJoin(sprintf('%s.mediaCategories', $filterDataDto->getEntityAlias()), 'mc')
                ->andWhere('mc.id = :mediaCategoryId')
                ->setParameter('mediaCategoryId', $filterDataDto->getValue());
        }
    }
}
