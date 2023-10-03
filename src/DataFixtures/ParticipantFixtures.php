<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Participant;
use Faker;

class ParticipantFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group10'];
    }

    public function load(ObjectManager $manager): void
    {

        $nombreDeParticipantAjoute = 10;

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < $nombreDeParticipantAjoute; $i++) {
        $Participant = new Participant();

        $Participant->setNom($faker->firstName()); 
        $Participant->setPrenom($faker->lastName());
        $Participant->setPseudo($faker->unique()->userName());
        $Participant->setTelephone($faker->phoneNumber());
        $Participant->setEmail($faker->unique()->email());
        $Participant->setPassword($faker->password());
        $Participant->setIsActif($faker->boolean(90));
        $Participant->setImage($faker->imageUrl(200, 200, true)); 

        // $Participant->addEstInscrit($faker->city);

        // $Participant->setCampus($faker->city);

        // $Participant->setRoles($faker->city);
        }
    $manager->flush();
        
    }
}
