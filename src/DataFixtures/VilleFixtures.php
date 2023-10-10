<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ville;
use Faker;

class VilleFixtures
{

    private $nombreDeVilleAjoute=0;

    public function __construct(int $nombreDeVilleAjoute)
    {
        $this->nombreDeVilleAjoute = $nombreDeVilleAjoute;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < $this->nombreDeVilleAjoute; $i++) {
        $ville = new Ville();

        $ville->setNom($faker->unique()->city); 
        $ville->setCodePostal($faker->unique()->postcode());
        $manager->persist($ville); 
        }
    $manager->flush();
        
    }
}
