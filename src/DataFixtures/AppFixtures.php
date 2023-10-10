<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
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
            new LieuFixtures($nombreDeLieuAjoute),
            new ParticipantFixtures($nombreDeParticipantAjoute),
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
