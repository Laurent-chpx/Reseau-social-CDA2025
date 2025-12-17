<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\EventImage;
use App\Entity\Promote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Créer des catégories
        $categories = [
            'Concert' => $this->createCategory($manager, 'Concert'),
            'Festival' => $this->createCategory($manager, 'Festival'),
            'Sport' => $this->createCategory($manager, 'Sport'),
            'Exposition' => $this->createCategory($manager, 'Exposition'),
            'Théâtre' => $this->createCategory($manager, 'Théâtre'),
            'Gastronomie' => $this->createCategory($manager, 'Gastronomie'),
            'Culturel' => $this->createCategory($manager, 'Culturel'),
        ];

        $manager->flush();

        // Récupérer les villes et utilisateurs
        $cities = $manager->getRepository('App\Entity\City')->findAll();
        $users = $manager->getRepository('App\Entity\User')->findAll();

        if (empty($cities) || empty($users)) {
            return;
        }

        // Créer des événements
        $eventsData = [
            [
                'title' => 'Festival de Jazz 2025',
                'description' => 'Le plus grand festival de jazz de la région revient pour une 15ème édition exceptionnelle. Retrouvez les plus grands artistes internationaux et découvrez les talents émergents de la scène jazz.',
                'city' => $this->findCityByName($cities, 'Toulouse'),
                'dateStart' => new \DateTimeImmutable('2025-06-15 18:00'),
                'dateEnd' => new \DateTimeImmutable('2025-06-17 23:00'),
                'schedule' => '18:00',
                'price' => 35.00,
                'categories' => [$categories['Concert'], $categories['Festival']],
                'sponsored' => true,
            ],
            [
                'title' => 'Fête de la musique 2025',
                'description' => 'Célébrez la musique sous toutes ses formes lors de cette journée festive. Concerts gratuits dans toute la ville, scènes ouvertes et animations pour petits et grands.',
                'city' => $this->findCityByName($cities, 'Toulouse'),
                'dateStart' => new \DateTimeImmutable('2025-06-21 14:00'),
                'dateEnd' => new \DateTimeImmutable('2025-06-21 23:59'),
                'schedule' => '14:00',
                'price' => null,
                'categories' => [$categories['Concert'], $categories['Festival']],
                'sponsored' => true,
            ],
            [
                'title' => 'Marathon de Paris',
                'description' => 'Participez au marathon le plus populaire de France ! 42,195 km à travers les plus beaux monuments de la capitale. Inscriptions limitées.',
                'city' => $this->findCityByName($cities, 'Paris'),
                'dateStart' => new \DateTimeImmutable('2025-04-07 09:00'),
                'dateEnd' => new \DateTimeImmutable('2025-04-07 16:00'),
                'schedule' => '09:00',
                'price' => 50.00,
                'categories' => [$categories['Sport']],
                'sponsored' => true,
            ],
            [
                'title' => 'Exposition Impressionnistes',
                'description' => 'Une exposition unique réunissant les plus grands chefs-d\'œuvre de l\'impressionnisme français. Monet, Renoir, Degas et bien d\'autres.',
                'city' => $this->findCityByName($cities, 'Paris'),
                'dateStart' => new \DateTimeImmutable('2025-03-01 10:00'),
                'dateEnd' => new \DateTimeImmutable('2025-06-30 18:00'),
                'schedule' => '10:00 - 18:00',
                'price' => 15.00,
                'categories' => [$categories['Exposition'], $categories['Culturel']],
                'sponsored' => false,
            ],
            [
                'title' => 'Nuit du Théâtre',
                'description' => 'Une nuit entière dédiée aux arts de la scène. Spectacles, performances, improvisations et rencontres avec les artistes.',
                'city' => $this->findCityByName($cities, 'Lyon'),
                'dateStart' => new \DateTimeImmutable('2025-05-10 20:00'),
                'dateEnd' => new \DateTimeImmutable('2025-05-11 06:00'),
                'schedule' => '20:00',
                'price' => 25.00,
                'categories' => [$categories['Théâtre'], $categories['Culturel']],
                'sponsored' => false,
            ],
            [
                'title' => 'Salon de la Gastronomie',
                'description' => 'Découvrez les saveurs de nos régions ! Dégustations, ateliers cuisine, rencontres avec des chefs étoilés et producteurs locaux.',
                'city' => $this->findCityByName($cities, 'Bordeaux'),
                'dateStart' => new \DateTimeImmutable('2025-09-15 10:00'),
                'dateEnd' => new \DateTimeImmutable('2025-09-17 19:00'),
                'schedule' => '10:00 - 19:00',
                'price' => 12.00,
                'categories' => [$categories['Gastronomie'], $categories['Culturel']],
                'sponsored' => false,
            ],
            [
                'title' => 'Rock en Seine 2025',
                'description' => 'Le festival rock incontournable de l\'été ! 3 jours de concerts avec les plus grands noms du rock international.',
                'city' => $this->findCityByName($cities, 'Paris'),
                'dateStart' => new \DateTimeImmutable('2025-08-22 15:00'),
                'dateEnd' => new \DateTimeImmutable('2025-08-24 23:00'),
                'schedule' => '15:00',
                'price' => 89.00,
                'categories' => [$categories['Concert'], $categories['Festival']],
                'sponsored' => true,
            ],
            [
                'title' => 'Tournoi de Tennis Amateur',
                'description' => 'Participez au grand tournoi de tennis amateur de la région. Toutes catégories, tous niveaux. Inscriptions ouvertes.',
                'city' => $this->findCityByName($cities, 'Nice'),
                'dateStart' => new \DateTimeImmutable('2025-07-05 09:00'),
                'dateEnd' => new \DateTimeImmutable('2025-07-08 18:00'),
                'schedule' => '09:00',
                'price' => 20.00,
                'categories' => [$categories['Sport']],
                'sponsored' => false,
            ],
        ];

        foreach ($eventsData as $eventData) {
            if ($eventData['city']) {
                $event = $this->createEvent(
                    $manager,
                    $eventData['title'],
                    $eventData['description'],
                    $eventData['city'],
                    $users[0],
                    $eventData['dateStart'],
                    $eventData['dateEnd'],
                    $eventData['schedule'],
                    $eventData['price'],
                    $eventData['categories'],
                    $eventData['sponsored']
                );
                $manager->persist($event);
            }
        }

        $manager->flush();
    }

    private function createCategory(ObjectManager $manager, string $name): Category
    {
        $category = new Category();
        $category->setName($name);
        $category->setSlug(strtolower(str_replace(' ', '-', $name)));
        $manager->persist($category);

        return $category;
    }

    private function createEvent(
        ObjectManager $manager,
        string $title,
        string $description,
        $city,
        $user,
        \DateTimeImmutable $dateStart,
        ?\DateTimeImmutable $dateEnd,
        ?string $schedule,
        ?float $price,
        array $categories,
        bool $sponsored
    ): Event {
        $event = new Event();
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setAddress($city->getName() . ', France');
        $event->setCity($city);
        $event->setCreatedBy($user);
        $event->setDateStart($dateStart);
        $event->setDateEnd($dateEnd);
        $event->setSchedule($schedule);
        $event->setPrice($price);
        $event->setCreatedAt(new \DateTimeImmutable());
        $event->setUpdatedAt(new \DateTimeImmutable());

        foreach ($categories as $category) {
            $event->addCategory($category);
        }

        // Si l'événement est sponsorisé, créer une promotion
        if ($sponsored) {
            $promote = new Promote();
            $promote->setEvent($event);
            $promote->setDateStart(new \DateTimeImmutable('-1 day'));
            $promote->setDateEnd(new \DateTimeImmutable('+30 days'));
            $promote->setPrice($price ?? 0);
            $manager->persist($promote);
        }

        return $event;
    }

    private function findCityByName(array $cities, string $name)
    {
        foreach ($cities as $city) {
            if ($city->getName() === $name) {
                return $city;
            }
        }
        return $cities[0] ?? null;
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            UserFixtures::class,
        ];
    }
}