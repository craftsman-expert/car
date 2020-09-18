<?php

namespace App\DataFixtures;

use App\Entity\Performer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PerformerFixtures extends Fixture
{
    static array $performers = [
        [
            "name" => "Michael Jackson",
            "reference_entity" => "michael_jackson"
        ],

        [
            "name" => "Mylene Farmer",
            "reference_entity" => "mylene_farmer"
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$performers as $item) {
            $performer = new Performer();
            $performer->setName($item["name"]);

            $this->setReference($item["reference_entity"], $performer);

            $manager->persist($performer);
        }

        $manager->flush();
    }
}
