<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use App\Entity\Lieu;
use App\Entity\Ville;
use Faker;

class LieuFixtures
{

    private int $nombreDeLieuAjoute=0;

    public function __construct(int $nombreDeLieuAjoute)
    {
        $this->nombreDeLieuAjoute = $nombreDeLieuAjoute;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        $existingVille = $manager->getRepository(Ville::class)->findAll(); //retourne un tableau des objets Ville de la db

        for ($i = 0; $i < $this->nombreDeLieuAjoute; $i++) {
        $lieu = new Lieu();

        $lieu->setNom($faker->unique()->city); 
        $lieu->setRue($faker->unique()->streetName); 

        $enableLocation = $faker->optional($weight = 0.5)->boolean;//50% null ($weight = 0.5)
        if ($enableLocation) {
            $lieu->setLatitude($faker->latitude($min = -90, $max = 90));
            $lieu->setLongitude($faker->longitude($min = -180, $max = 180)); 
        } else {
            $lieu->setLatitude(null);
            $lieu->setLongitude(null);
        }

            //choisi de d'un id ville valid
        if (!empty($existingVille)) {
            $lieu->setVille($faker->randomElement($existingVille));
        } else {
            exit("Aucune ville n'existe");
        }

        $manager->persist($lieu);
        }   
    $manager->flush();
    }
}
