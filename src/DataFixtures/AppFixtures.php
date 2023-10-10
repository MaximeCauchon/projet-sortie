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


class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $nombreDeVilleAjoute = 8;
        $nombreDeLieuAjoute = 20;
        $nombreDeParticipantAjoute = 50;
        $nombreDeSortieAjoute = 100;
        $pourcentSortiePasse = 40;
        $pourcentSortiePresente = 30;

        // Chargez les fixtures dans l'ordre de votre choix

            $EtatFixtures = new EtatFixtures();
            $EtatFixtures->load($manager);
            $CampusFixtures = new CampusFixtures();
            $CampusFixtures->load($manager);
            $VilleFixtures = new VilleFixtures($nombreDeVilleAjoute);
            $VilleFixtures->load($manager);
            $LieuFixtures = new LieuFixtures($nombreDeLieuAjoute);
            $LieuFixtures->load($manager);
            $ParticipantFixtures = new ParticipantFixtures($nombreDeParticipantAjoute, $this->passwordHasher);
            $ParticipantFixtures->load($manager);
            $SortieFixtures = new SortieFixtures($nombreDeSortieAjoute, $pourcentSortiePasse, $pourcentSortiePresente);
            $SortieFixtures->load($manager);
            $AdminFixtures = new AdminFixtures($this->passwordHasher);
            $AdminFixtures->load($manager);

    }


}