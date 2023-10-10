<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Participant;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group10'];
    }

    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {

        $existingCampus = $manager->getRepository(campus::class)->findAll(); //retourne un tableau des objets Ville de la db
        $faker = Faker\Factory::create('fr_FR');

        $Participant = new Participant();

        $Participant->setNom("admin1Nom"); 
        $Participant->setPrenom("admin1Prenom");
        $Participant->setPseudo("admin1");
        $Participant->setTelephone($faker->phoneNumber());
        $Participant->setEmail("admin1@admin.com");
        $password = "admin1admin1";
        $Participant->setPassword(
            $this->passwordEncoder->hashPassword(
                $Participant,
                $password
        ));
        $Participant->setIsActif(true);
        $Participant->setImage($faker->imageUrl(10, 10, true)); 
        $Participant->setRoles(["ROLE_ADMIN"]);
        
        if (!empty($existingCampus)) {
            $Participant->setCampus($faker->randomElement($existingCampus));
        } else {
            exit("Aucune campus n'existe");
        }

    $manager->flush();
        
    }
}
