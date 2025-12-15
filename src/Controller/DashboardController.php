<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        // TODO: Replace with real data from database
        $userEventsCount = 17; // Count of events created by user
        $userSubscribersCount = 240; // Count of subscribers to user

        // TODO: Fetch recent events from user's cities
        $recentEvents = [
            [
                'title' => 'Concert de jazz',
                'location' => 'Parc Montpellier',
                'date' => '22 novembre 2025',
            ],
            [
                'title' => 'Concert de jazz',
                'location' => 'Parc Montpellier',
                'date' => '22 novembre 2025',
            ],
        ];

        // TODO: Fetch activities from favorite creators
        $activities = [
            [
                'creator' => 'Mairie de Toulouse',
                'event' => 'Festival de printemps',
                'time' => 'il y a 2h',
            ],
            [
                'creator' => 'Association Sports',
                'event' => 'Marathon urbain',
                'time' => 'il y a 8h',
            ],
            [
                'creator' => 'Club Photo Montpellier',
                'event' => 'Exposition photos',
                'time' => 'Hier',
            ],
        ];

        return $this->render('dashboard.html.twig', [
            'user_events_count' => $userEventsCount,
            'user_subscribers_count' => $userSubscribersCount,
            'recent_events' => $recentEvents,
            'activities' => $activities,
        ]);
    }
}
