<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture
{

    public const DEPARTMENTS = [
        ['code' => '09', 'name' => 'Ariège'],
        ['code' => '11', 'name' => 'Aude'],
        ['code' => '12', 'name' => 'Aveyron'],
        ['code' => '30', 'name' => 'Gard'],
        ['code' => '31', 'name' => 'Haute-Garonne'],
        ['code' => '32', 'name' => 'Gers'],
        ['code' => '34', 'name' => 'Hérault'],
        ['code' => '46', 'name' => 'Lot'],
        ['code' => '48', 'name' => 'Lozère'],
        ['code' => '65', 'name' => 'Hautes-Pyrénées'],
        ['code' => '66', 'name' => 'Pyrénées-Orientales'],
        ['code' => '81', 'name' => 'Tarn'],
        ['code' => '82', 'name' => 'Tarn-et-Garonne'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEPARTMENTS as $data) {
            $department = new Department();
            $department->setCode($data['code']);
            $department->setName($data['name']);

            $manager->persist($department);

            // Référence pour les fixtures de villes
            $this->addReference('dept_' . $data['code'], $department);
        }

        $manager->flush();
    }
}
