<?php

namespace App\DataFixtures;

use App\Entity\AudioRecord;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AudioRecordFixtures extends Fixture implements DependentFixtureInterface
{
    static array $audioRecords = [
        [
            "name" => "Billie Jean",
            "reference_performer" => "michael_jackson",
            "path" => "/michael-jackson/billie_jean_389278325682.mp3"
        ],

        [
            "name" => "Appelle Mon Numero",
            "reference_performer" => "mylene_farmer",
            "path" => "/mylene-farmer/appelle_mon_numero_389278325682.mp3"
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$audioRecords as $item) {
            $audioRecord = new AudioRecord();
            $audioRecord->setName($item["name"]);
            $audioRecord->setPath($item["path"]);
            $audioRecord->setPerformer($this->getReference($item["reference_performer"]));

            $manager->persist($audioRecord);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            PerformerFixtures::class
        ];
    }
}
