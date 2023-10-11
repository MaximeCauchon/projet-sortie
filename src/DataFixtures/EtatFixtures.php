<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Etat;

class EtatFixtures 
{

    public function load(ObjectManager $manager): void
    {
        $creee = new Etat();
        $creee->setLibelle("Créée"); 
        $manager->persist($creee);

        $ouverte = new Etat();
        $ouverte->setLibelle("Ouverte");
        $manager->persist($ouverte); 

        $cloturee = new Etat();
        $cloturee->setLibelle("Clôturée");
        $manager->persist($cloturee); 

        $activiteEnCours = new Etat();
        $activiteEnCours->setLibelle("En cours");
        $manager->persist($activiteEnCours); 

        $passee = new Etat();
        $passee->setLibelle("Passée");
        $manager->persist($passee);

        $historisee = new Etat();
        $historisee->setLibelle("Historisée");
        $manager->persist($historisee); 

        $annulee = new Etat();
        $annulee->setLibelle("Annulée");
        $manager->persist($annulee); 

    $manager->flush();
        
    }
}
