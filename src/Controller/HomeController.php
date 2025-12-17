<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DepartmentRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
            'departments' => $departmentRepository->findBy([], ['code' => 'ASC']),
        ]);
    }

    #[Route('/api/events', name: 'app_home_events', methods: ['GET'])]
    public function loadEvents(
        Request $request,
        EventRepository $eventRepository
    ): JsonResponse {
        $departmentId = $request->query->get('department');
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 3);

        $events = $eventRepository->findByDepartmentWithPromotedFirst($departmentId, $offset, $limit);
        $total = $eventRepository->countByDepartment($departmentId);

        $now = new \DateTimeImmutable();
        $eventsData = [];
        foreach ($events as $event) {
            // Vérifier si l'event est promu actuellement
            $promote = $event->getPromote();
            $isPromoted = $promote && $promote->getDateStart() <= $now && $promote->getDateEnd() >= $now;

            $eventsData[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'city' => $event->getCity()->getName(),
                'description' => $event->getDescription(),
                'departmentCode' => $event->getCity()->getDepartment()->getCode(),
                'dateStart' => $event->getDateStart()->format('d/m/Y à H:i'),
                'isPromoted' => $isPromoted,
                'image' => $event->getImages()->count() > 0
                    ? '/uploads/events/' . $event->getImages()->first()->getUrl()
                    : '/images/placeholder.jpg',
                'categories' => array_map(fn($cat) => $cat->getName(), $event->getCategories()->toArray()),
            ];
        }

        return new JsonResponse([
            'events' => $eventsData,
            'hasMore' => ($offset + $limit) < $total,
            'total' => $total,
        ]);
    }

}
