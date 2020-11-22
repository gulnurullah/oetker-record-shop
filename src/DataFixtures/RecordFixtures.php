<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Record;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RecordFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('de_DE');

        for ($i=0; $i < 10; $i++){
            $record = new Record();
            $record->setName($faker->name);
            $record->setArtist($faker->firstName . ' ' . $faker->lastName);
            $record->setDescription($faker->text());
            $record->setCreatedAt(new DateTime('now'));

            $manager->persist($record);
        }

        $manager->flush();
    }
}
