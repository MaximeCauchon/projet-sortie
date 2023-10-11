<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures
{
    private $nombreDeParticipantAjoute=0;
    private $passwordEncoder;

    public function __construct(int $nombreDeParticipantAjoute, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->nombreDeParticipantAjoute = $nombreDeParticipantAjoute;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $existingCampus = $manager->getRepository(Campus::class)->findAll(); //retourne un tableau des objets Ville de la db
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < $this->nombreDeParticipantAjoute; $i++) {
        $Participant = new Participant();

        $name = $faker->lastName();
        $Participant->setNom($name); 
        $Participant->setPrenom($faker->firstName());
        $Participant->setPseudo($faker->unique()->userName());
        $Participant->setTelephone($faker->phoneNumber());
        $Participant->setEmail($faker->unique()->email());
        $password = $name."pass";
        $Participant->setPassword(
            $this->passwordEncoder->hashPassword(
                $Participant,
                $password
        ));
        $Participant->setIsActif($faker->boolean(90));//90% d'actif
        // $Participant->setImage($faker->imageUrl(10, 10, true)); 
        $Participant->setRoles(["ROLE_USER"]);
        
        if (!empty($existingCampus)) {
            $Participant->setCampus($faker->randomElement($existingCampus));
        } else {
            exit("Aucune campus n'existe");
        }
        $manager->persist($Participant);
        }
    $manager->flush();
        
    }
}
