<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Données fictives pour les événements sponsorisés
        $sponsoredEvents = [
            [
                'id' => 1,
                'title' => 'Festival de Jazz 2025',
                'location' => 'Toulouse (31)',
                'date' => '15 juin 2025 à 18h00',
                'image' => 'festival-jazz.jpg', // À remplacer par le vrai chemin
                'tags' => ['Concert', 'Festival'],
                'sponsored' => true
            ],
            [
                'id' => 2,
                'title' => 'Fête de la musique 2025',
                'location' => 'Toulouse (31)',
                'date' => '21 juin 2025',
                'image' => 'fete-musique.jpg',
                'tags' => ['Concert', 'Festival'],
                'sponsored' => true
            ],
            [
                'id' => 3,
                'title' => 'Marathon de Paris',
                'location' => 'Paris (75)',
                'date' => '7 avril 2025',
                'image' => 'marathon-paris.jpg',
                'tags' => ['Sport'],
                'sponsored' => true
            ]
        ];

        // Statistiques de la plateforme
        $stats = [
            'users' => '12,487',
            'events' => '3,621',
            'departments' => '94',
            'organizers' => '1,247'
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
            'sponsored_events' => $sponsoredEvents,
            'stats' => $stats,
            'features' => $features,
            'page_title' => 'Découvrez les événements près de chez vous'
        ]);
    }
}