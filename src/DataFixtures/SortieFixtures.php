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

class SortieFixtures
{

    private $nombreDeSortieAjoute;
    private $pourcentSortiePasse;
    private $pourcentSortiepresente;

    public function __construct(int $nombreDeSortieAjoute, int $pourcentSortiePasse, int $pourcentSortiepresente)
    {
        $this->nombreDeSortieAjoute = $nombreDeSortieAjoute;
        $this->pourcentSortiePasse = $pourcentSortiePasse;
        $this->pourcentSortiepresente = $pourcentSortiepresente;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < $this->nombreDeSortieAjoute; $i++) {
        $sortie = new Sortie();

        $existingOrganisateur = $manager->getRepository(Participant::class)->findActif(); //retourne un tableau des objets Organisateur de la db
        $existingParticipant = $manager->getRepository(Participant::class)->findAll();
        $existingEtat = $manager->getRepository(Etat::class)->findAll();
        $existingLieu = $manager->getRepository(Lieu::class)->findAll();
        $existingCampus = $manager->getRepository(Campus::class)->findAll();
        
        //TODO: si etat = créée -> pas d'indcrit
        //TODO: si et seulement si etat = annulée -> commentaire annulation
        if (!empty($existingEtat)) {
            $etat = $faker->randomElement($existingEtat);
            $sortie->setEtat($etat);
        } else {
            exit("Aucune etat n'existe");
        }

        $sortie->setNom($faker->words(5, true));
        $sortie->setInfosSortie($faker->paragraph(3));//paragraphe de 3 phrase a +-40% prés
        $sortie->setMotifAnnulation($faker->paragraph(3));
        $dureeInSeconds = mt_rand(0, 8 * 3600); // Génère une durée aléatoire entre 0 et 8 heures en secondes
        $sortie->setDuree(new \DateInterval('PT' . $dureeInSeconds . 'S'));
        
        if (!empty($existingOrganisateur)) {
            $sortie->setOrganisateur($faker->randomElement($existingOrganisateur));
        } else {
            exit("Aucune organisateur(participant) n'existe");
        }

        if (!empty($existingLieu)) {
            $sortie->setLieu($faker->randomElement($existingLieu));
        } else {
            exit("Aucune lieu n'existe");
        }

        if (!empty($existingCampus)) {
            $sortie->setCampus($faker->randomElement($existingCampus));
        } else {
            exit("Aucune campus n'existe");
        }

        //creer un random number < setNbInscriptionMax
        $NbInscriptionMax = $faker->numberBetween(1,30);
        $NbInscription= $faker->numberBetween(1,$NbInscriptionMax);

        $sortie->setNbInscriptionMax($NbInscriptionMax);
        if (!empty($existingParticipant)) {
            for ($j = 0; $j < $NbInscription; $j++) {
                $sortie->addParticipant($faker->randomElement($existingParticipant));
            }
        } else {
            exit("Aucune campus n'existe");
        }

        $passeOuFutur = $faker->numberBetween(1,100);
        if ($this->pourcentSortiePasse > $passeOuFutur){ 
            //cas sortie dans le passée
            $DateHeureDebut = $faker->dateTimeBetween('-2 years', 'now');
            $DateLimiteInscription = $faker->dateTimeBetween('-3 years', '-2years');
            $sortie->setDateHeureDebut($DateHeureDebut);
            $sortie->setDateLimiteInscription($DateLimiteInscription);
        } elseif ($this->pourcentSortiepresente > $passeOuFutur){
            //cas sortie dans le présent
            $DateHeureDebut = $faker->dateTimeBetween('now', '+2 week');
            $DateLimiteInscription = $faker->dateTimeBetween('-2 week', 'now');
            $sortie->setDateHeureDebut($DateHeureDebut);
            $sortie->setDateLimiteInscription($DateLimiteInscription);
        }else {
            //cas sortie dans le futur
            $DateHeureDebut = $faker->dateTimeBetween('+2 week', '+1 years');
            $DateLimiteInscription = $faker->dateTimeBetween('now', '+2 week');
            $sortie->setDateHeureDebut($DateHeureDebut);
            $sortie->setDateLimiteInscription($DateLimiteInscription);
        }       
        $manager->persist($sortie);
        }
    $manager->flush();
        
    }
}
