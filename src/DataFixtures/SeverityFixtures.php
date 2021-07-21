<?php

namespace App\DataFixtures;

use App\Entity\Severity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SeverityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $severities = [
            [ 'id' => 1, 'name' => 'Info', 'selected' => false ],
            [ 'id' => 3, 'name' => 'Low', 'selected' => false ],
            [ 'id' => 2, 'name' => 'Normal', 'selected' => true ],
            [ 'id' => 4, 'name' => 'High', 'selected' => false ],
            [ 'id' => 5, 'name' => 'Urgent', 'selected' => false ],
        ];

        foreach ($severities as $value) {
            $severity = new Severity();
            $severity->setId($value['id']);
            $severity->setName($value['name']);
            $severity->setIsDefault($value['selected']);

            $manager->persist($severity);
        }

        $manager->flush();
    }
}
