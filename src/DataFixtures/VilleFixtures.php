<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ville;
use Faker;

class VilleFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group2'];
    }

    public function load(ObjectManager $manager): void
    {

        $nombreDeVilleAjoute = 5;

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < $nombreDeVilleAjoute; $i++) {
        $ville = new Ville();

        $ville->setNom($faker->unique()->city); 
        $ville->setCodePostal($faker->unique()->postcode());
        $manager->persist($ville); 
        }
    $manager->flush();
        
    }
}
