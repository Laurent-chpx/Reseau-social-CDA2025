<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    public const CITIES = [
        // Ariège (09)
        ['name' => 'Foix', 'postalCode' => '09000', 'dept' => '09'],
        ['name' => 'Pamiers', 'postalCode' => '09100', 'dept' => '09'],
        ['name' => 'Saint-Girons', 'postalCode' => '09200', 'dept' => '09'],

        // Aude (11)
        ['name' => 'Carcassonne', 'postalCode' => '11000', 'dept' => '11'],
        ['name' => 'Narbonne', 'postalCode' => '11100', 'dept' => '11'],
        ['name' => 'Castelnaudary', 'postalCode' => '11400', 'dept' => '11'],
        ['name' => 'Limoux', 'postalCode' => '11300', 'dept' => '11'],

        // Aveyron (12)
        ['name' => 'Rodez', 'postalCode' => '12000', 'dept' => '12'],
        ['name' => 'Millau', 'postalCode' => '12100', 'dept' => '12'],
        ['name' => 'Villefranche-de-Rouergue', 'postalCode' => '12200', 'dept' => '12'],

        // Gard (30)
        ['name' => 'Nîmes', 'postalCode' => '30000', 'dept' => '30'],
        ['name' => 'Alès', 'postalCode' => '30100', 'dept' => '30'],
        ['name' => 'Bagnols-sur-Cèze', 'postalCode' => '30200', 'dept' => '30'],
        ['name' => 'Beaucaire', 'postalCode' => '30300', 'dept' => '30'],

        // Haute-Garonne (31)
        ['name' => 'Toulouse', 'postalCode' => '31000', 'dept' => '31'],
        ['name' => 'Blagnac', 'postalCode' => '31700', 'dept' => '31'],
        ['name' => 'Colomiers', 'postalCode' => '31770', 'dept' => '31'],
        ['name' => 'Tournefeuille', 'postalCode' => '31170', 'dept' => '31'],
        ['name' => 'Muret', 'postalCode' => '31600', 'dept' => '31'],
        ['name' => 'Saint-Gaudens', 'postalCode' => '31800', 'dept' => '31'],

        // Gers (32)
        ['name' => 'Auch', 'postalCode' => '32000', 'dept' => '32'],
        ['name' => 'Condom', 'postalCode' => '32100', 'dept' => '32'],
        ['name' => 'Fleurance', 'postalCode' => '32500', 'dept' => '32'],

        // Hérault (34)
        ['name' => 'Montpellier', 'postalCode' => '34000', 'dept' => '34'],
        ['name' => 'Béziers', 'postalCode' => '34500', 'dept' => '34'],
        ['name' => 'Sète', 'postalCode' => '34200', 'dept' => '34'],
        ['name' => 'Agde', 'postalCode' => '34300', 'dept' => '34'],
        ['name' => 'Lunel', 'postalCode' => '34400', 'dept' => '34'],

        // Lot (46)
        ['name' => 'Cahors', 'postalCode' => '46000', 'dept' => '46'],
        ['name' => 'Figeac', 'postalCode' => '46100', 'dept' => '46'],
        ['name' => 'Gourdon', 'postalCode' => '46300', 'dept' => '46'],

        // Lozère (48)
        ['name' => 'Mende', 'postalCode' => '48000', 'dept' => '48'],
        ['name' => 'Marvejols', 'postalCode' => '48100', 'dept' => '48'],
        ['name' => 'Florac', 'postalCode' => '48400', 'dept' => '48'],

        // Hautes-Pyrénées (65)
        ['name' => 'Tarbes', 'postalCode' => '65000', 'dept' => '65'],
        ['name' => 'Lourdes', 'postalCode' => '65100', 'dept' => '65'],
        ['name' => 'Bagnères-de-Bigorre', 'postalCode' => '65200', 'dept' => '65'],

        // Pyrénées-Orientales (66)
        ['name' => 'Perpignan', 'postalCode' => '66000', 'dept' => '66'],
        ['name' => 'Canet-en-Roussillon', 'postalCode' => '66140', 'dept' => '66'],
        ['name' => 'Saint-Cyprien', 'postalCode' => '66750', 'dept' => '66'],
        ['name' => 'Argelès-sur-Mer', 'postalCode' => '66700', 'dept' => '66'],

        // Tarn (81)
        ['name' => 'Albi', 'postalCode' => '81000', 'dept' => '81'],
        ['name' => 'Castres', 'postalCode' => '81100', 'dept' => '81'],
        ['name' => 'Gaillac', 'postalCode' => '81600', 'dept' => '81'],
        ['name' => 'Mazamet', 'postalCode' => '81200', 'dept' => '81'],

        // Tarn-et-Garonne (82)
        ['name' => 'Montauban', 'postalCode' => '82000', 'dept' => '82'],
        ['name' => 'Castelsarrasin', 'postalCode' => '82100', 'dept' => '82'],
        ['name' => 'Moissac', 'postalCode' => '82200', 'dept' => '82'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CITIES as $data) {
            $city = new City();
            $city->setName($data['name']);
            $city->setPostalCode($data['postalCode']);
            $city->setDepartment($this->getReference('dept_' . $data['dept'], Department::class));

            $manager->persist($city);

            // Référence pour les futures fixtures (events, etc.)
            $this->addReference('city_' . $data['postalCode'], $city);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            DepartmentFixtures::class,
        ];
    }
}
