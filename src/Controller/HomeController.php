<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        EventRepository $eventRepository,
        UserRepository $userRepository,
        DepartmentRepository $departmentRepository
    ): Response
    {
        // Récupérer les événements sponsorisés (avec promotion)
        $allEvents = $eventRepository->findAll();
        $sponsoredEventsData = [];

        foreach ($allEvents as $event) {
            if ($event->getPromote() !== null) {
                // Formater les catégories
                $tags = [];
                foreach ($event->getCategories() as $category) {
                    $tags[] = $category->getName();
                }

                // Formater la date
                $dateFormatted = $event->getDateStart()->format('d/m/Y');
                if ($event->getSchedule()) {
                    $dateFormatted .= ' à ' . $event->getSchedule();
                }

                // Formater la localisation
                $location = $event->getCity()->getName();
                if ($event->getCity()->getDepartment()) {
                    $location .= ' (' . $event->getCity()->getDepartment()->getCode() . ')';
                }

                $sponsoredEventsData[] = [
                    'id' => $event->getId(),
                    'title' => $event->getTitle(),
                    'location' => $location,
                    'date' => $dateFormatted,
                    'image' => $event->getImages()->first() ? $event->getImages()->first()->getPath() : null,
                    'tags' => $tags,
                    'sponsored' => true
                ];
            }
        }

        // Si pas d'événements sponsorisés, prendre les 3 derniers événements
        if (empty($sponsoredEventsData)) {
            $recentEvents = $eventRepository->findBy([], ['createdAt' => 'DESC'], 3);

            foreach ($recentEvents as $event) {
                $tags = [];
                foreach ($event->getCategories() as $category) {
                    $tags[] = $category->getName();
                }

                $dateFormatted = $event->getDateStart()->format('d/m/Y');
                if ($event->getSchedule()) {
                    $dateFormatted .= ' à ' . $event->getSchedule();
                }

                $location = $event->getCity()->getName();
                if ($event->getCity()->getDepartment()) {
                    $location .= ' (' . $event->getCity()->getDepartment()->getCode() . ')';
                }

                $sponsoredEventsData[] = [
                    'id' => $event->getId(),
                    'title' => $event->getTitle(),
                    'location' => $location,
                    'date' => $dateFormatted,
                    'image' => $event->getImages()->first() ? $event->getImages()->first()->getPath() : null,
                    'tags' => $tags,
                    'sponsored' => false
                ];
            }
        }

        // Limiter à 3 événements
        $sponsoredEventsData = array_slice($sponsoredEventsData, 0, 3);

        // Statistiques réelles de la plateforme
        $stats = [
            'users' => number_format($userRepository->count([]), 0, ',', ' '),
            'events' => number_format($eventRepository->count([]), 0, ',', ' '),
            'departments' => number_format($departmentRepository->count([]), 0, ',', ' '),
            'organizers' => number_format($userRepository->count([]), 0, ',', ' ')
        ];

        // Features (Comment ça marche)
        $features = [
            [
                'icon' => '🔍',
                'title' => 'Découvrez',
                'description' => 'Parcourez des milliers d\'événements dans votre ville ou département'
            ],
            [
                'icon' => '⭐',
                'title' => 'Suivez',
                'description' => 'Abonnez-vous aux créateurs et recevez leurs nouveaux événements'
            ],
            [
                'icon' => '📅',
                'title' => 'Participez',
                'description' => 'Ne ratez plus rien grâce aux notifications personnalisées'
            ],
            [
                'icon' => '🎉',
                'title' => 'Créez',
                'description' => 'Organisateur ? Publiez vos événements et touchez plus de monde'
            ]
        ];

        return $this->render('index.html.twig', [
            'sponsored_events' => $sponsoredEventsData,
            'stats' => $stats,
            'features' => $features,
            'page_title' => 'Découvrez les événements près de chez vous'
        ]);
    }
}