<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_SUPER_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Usuarios')
            ->setEntityLabelInSingular('Usuario')
            ->setEntityLabelInPlural('Usuarios')
            ->showEntityActionsInlined()
            ->setDefaultSort(['id' => 'DESC', 'name' => 'ASC']);

        // in DETAIL and EDIT pages, the closure receives the current entity
        // as the first argument
        // ->setPageTitle('detail', fn (Product $product) => (string) $product)
        // ->setPageTitle('edit', fn (Category $category) => sprintf('Editing <b>%s</b>', $category->getName()))

        // the help message displayed to end users (it can contain HTML tags)
        // ->setHelp('edit', '...');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('username', 'Nombre de usuario')->setDefaultColumns('col-md-7 col-xxl-6'),
            TextField::new('plainPassword', 'Contraseña')
                ->setDefaultColumns('col-md-7 col-xxl-6')
                ->onlyOnForms(),
            TextField::new('name', 'Nombre')->setDefaultColumns('col-md-7 col-xxl-6'),
            EmailField::new('email', 'E-mail')->setDefaultColumns('col-md-7 col-xxl-6'),
            BooleanField::new('isVerified', 'Verificado')
                ->onlyOnForms()
                ->renderAsSwitch(false)
                ->setDefaultColumns('col-md-7 col-xxl-6'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('id', 'ID'))
            ->add(TextFilter::new('name', 'Nombre'))
            ->add(TextFilter::new('username', 'Nombre de usuario'))
            ->add(TextFilter::new('roles', 'Roles'))
            ->add(TextFilter::new('email', 'E-mail'))
            ->add(BooleanFilter::new('isVerified', 'Verificado'));
    }

    public function createEntity(string $entityFqcn)
    {
        return new $entityFqcn();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getPlainPassword()) {
            $entityInstance->setPassword(
                $this->userPasswordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword()),
            );

            $entityInstance->eraseCredentials();
        }

        $entityInstance->setRoles(['ROLE_ADMIN']);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getPlainPassword()) {
            $entityInstance->setPassword(
                $this->userPasswordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword()),
            );

            $entityInstance->eraseCredentials();
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            // this will forbid to create or delete entities in the backend
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
