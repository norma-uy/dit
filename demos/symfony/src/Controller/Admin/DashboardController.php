<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\MediaCategory;
use App\Entity\MediaCollection;
use App\Entity\Page;
use App\Entity\Post;
use App\Entity\PostCategory;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/{_locale}', requirements: ['_locale' => 'es|en'])]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        // return parent::index();

        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            ->setTitle('Blog Admin')
            // you can include HTML contents too (e.g. to link to an image)
            // ->setTitle(
            //     '<img src="..."> ACME <span class="text-small">Corp.</span>',
            // )

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
            ->setFaviconPath('/build/website/images/favicon.svg')

            // the domain used by default is 'messages'
            ->setTranslationDomain('EasyAdminBundle')

            // there's no need to define the "text direction" explicitly because
            // its default value is inferred dynamically from the user locale
            // ->setTextDirection('ltr')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            // ->renderSidebarMinimized()

            // by default, users can select between a "light" and "dark" mode for the
            // backend interface. Call this method if you prefer to disable the "dark"
            // mode for any reason (e.g. if your interface customizations are not ready for it)
            ->disableDarkMode()

            // by default, all backend URLs are generated as absolute URLs. If you
            // need to generate relative URLs instead, call this method
            ->generateRelativeUrls();
        //
    }

    public function configureMenuItems(): iterable
    {
        /*  yield MenuItem::linktoRoute(
            'De regreso a la web',
            'fa fa-left-to-line',
            'app_home',
        ); */

        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToCrud('Archivos', 'fas fa-photo-video', Media::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud(
            'Categoría de Archivos',
            'fas fa-grip-vertical',
            MediaCategory::class,
        )->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud('Colecciones', 'fas fa-layer-group', MediaCollection::class)->setPermission(
            'ROLE_ADMIN',
        );

        yield MenuItem::linkToCrud('Páginas', 'fas fa-pager', Page::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud('Post', 'fas fa-pager', Post::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud('Categorías', 'fas fa-share-nodes', PostCategory::class)->setPermission(
            'ROLE_ADMIN',
        );

        yield MenuItem::linkToCrud('Usuarios', 'fas fa-users', User::class)->setPermission('ROLE_SUPER_ADMIN');
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return UserMenu
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user)
            // use this method if you don't want to display the name of the user
            ->displayUserName(true)

            // you can return an URL with the avatar image
            // ->setAvatarUrl('https://...')
            // ->setAvatarUrl($user->getProfileImageUrl())
            // use this method if you don't want to display the user image
            ->displayUserAvatar(false)
            // you can also pass an email address to use gravatar's service
            ->setGravatarEmail($user->getEmail() || '')

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                // MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                // MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                // MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }
}
