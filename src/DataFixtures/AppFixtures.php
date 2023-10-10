<?php

namespace App\DataFixtures;

use App\DataFixtures\EtatFixtures;
use App\DataFixtures\LieuFixtures;
use App\DataFixtures\AdminFixtures;
use App\DataFixtures\VilleFixtures;
use App\DataFixtures\CampusFixtures;
use App\DataFixtures\SortieFixtures;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\ParticipantFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['appFixture'];
    }

    public function load(ObjectManager $manager)
    {
        $passwordEncoder = new UserPasswordHasherInterface();
        $nombreDeVilleAjoute = 8;
        $nombreDeLieuAjoute = 20;
        $nombreDeParticipantAjoute = 50;
        $nombreDeSortieAjoute = 100;
        $pourcentSortiePasse = 40;
        $pourcentSortiepresente = 30;

        // Chargez les fixtures dans l'ordre de votre choix
        $this->loadFixtures($manager, [
            EtatFixtures::class,
            CampusFixtures::class,
            new VilleFixtures($nombreDeVilleAjoute),
            // new LieuFixtures($nombreDeLieuAjoute),
            new ParticipantFixtures($nombreDeParticipantAjoute, $passwordEncoder),
            new SortieFixtures($nombreDeSortieAjoute, $pourcentSortiePasse, $pourcentSortiepresente),
            AdminFixtures::class,
        ]);
    }

    private function loadFixtures(ObjectManager $manager, array $fixtures)
    {
        foreach ($fixtures as $fixtureClass) {
            $fixture = new $fixtureClass();
            $fixture->load($manager);
        }
    }
}
