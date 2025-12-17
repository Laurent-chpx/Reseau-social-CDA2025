<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        EventRepository $eventRepository,
        CategoryRepository $categoryRepository,
        DepartmentRepository $departmentRepository,
        CityRepository $cityRepository
    ): Response {

        $search = $request->query->get('search');
        $categoryId = $request->query->get('category');
        $departmentId = $request->query->get('department');


        if ($search || $categoryId || $departmentId) {
            return $this->redirectToRoute('app_event_index', [
                'search' => $search,
                'category' => $categoryId,
                'department' => $departmentId,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'latestEvents' => $eventRepository->findLatest(3),
            'promotedEvents' => $eventRepository->findLatestPromoted(3),
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
            'departments' => $departmentRepository->findBy([], ['code' => 'ASC']),
        ]);
    }
}
