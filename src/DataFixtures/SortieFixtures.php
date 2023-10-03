<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Etat;
use App\Entity\Campus;
use App\Entity\Participant;
use Faker;

class SortieFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group10'];
    }

    public function load(ObjectManager $manager): void
    {

        $nombreDeSortieAjoute = 15;

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < $nombreDeSortieAjoute; $i++) {
        $sortie = new Sortie();

        $sortie->setNom($faker->unique()->city);  
        $sortie->setNbInscriptionMax($faker->randomNumber(2, false));
        $sortie->setInfosSortie($faker->paragraph(5));
        $sortie->setMotifAnnulation($faker->sentence(15));

        // $sortie->setDuree($faker->dateTimeInInterval('-1 week', '+3 days'));

        //TO DO verifier si l'organisateur n'est pas un compte désactivé
        $existingOrganisateur = $manager->getRepository(Participant::class)->findAll(); //retourne un tableau des objets Lieu de la db
        if (!empty($existingOrganisateur)) {
            $sortie->setOrganisateur($faker->randomElement($existingOrganisateur));
        } else {
            exit("Aucune organisateur(participant) n'existe");
        }

        
        $existingLieu = $manager->getRepository(Lieu::class)->findAll(); //retourne un tableau des objets Lieu de la db
        if (!empty($existingLieu)) {
            $sortie->setLieu($faker->randomElement($existingLieu));
        } else {
            exit("Aucune lieu n'existe");
        }
               
        // $sortie->addParticipant($faker->unique()->postcode());

        // $sortie->setEtat($faker->unique()->postcode());
        // $sortie->setDateHeureDebut($faker->dateTimeBetween('-1 week', '+1 week'));
        // $sortie->setDateLimiteInscription($faker->unique()->postcode());

        $manager->persist($sortie);
        }
    $manager->flush();
        
    }
}
