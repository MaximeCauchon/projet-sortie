<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use App\Entity\Lieu;
use App\Entity\Ville;
use Faker;

class LieuFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group1'];
    }

    public function load(ObjectManager $manager): void
    {

        $nombreDeLieuAjoute = 5;

        $faker = Faker\Factory::create('fr_FR');

        $existingVille = $manager->getRepository(ville::class)->findAll(); //retourne un tableau des objets Ville de la db

        for ($i = 0; $i < $nombreDeLieuAjoute; $i++) {
        $lieu = new Lieu();

        $lieu->setNom($faker->unique()->city); 
        $lieu->setRue($faker->unique()->streetName); 
        // TO DO attention, les %null entre Latitude et Longitude sont indépandants
        $lieu->setLatitude($faker->optional($weight = 0.5)->latitude($min = -90, $max = 90));//50% null ($weight = 0.5)
        $lieu->setLongitude($faker->optional($weight = 0.5)->longitude($min = -90, $max = 90));//50% null

            //choisi de d'un id ville valid
        if (!empty($existingVille)) {
            // $randomVille = $existingVille[array_rand($existingVille)];
            $lieu->setVille($faker->randomElement($existingVille));
        } else {
            exit("Aucune ville n'existe");
        }

        // Enregistrez l'entité User dans la base de données.
        $manager->persist($lieu);
        }   
    $manager->flush();
    }
}
