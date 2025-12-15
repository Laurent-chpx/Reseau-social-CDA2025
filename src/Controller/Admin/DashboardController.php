<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Department;
use App\Entity\City;
use App\Entity\Category;
use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        yield MenuItem::section('Localisation');
        yield MenuItem::linkToCrud('Départements', 'fas fa-map', Department::class);
        yield MenuItem::linkToCrud('Villes', 'fas fa-city', City::class);

        yield MenuItem::section('Événements');
        yield MenuItem::linkToCrud('Categories', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Événements', 'fas fa-calendar', Event::class);
    }
}
