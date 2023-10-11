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

        //TODO: faire en sorte que l'ID soit fixe et ne s'auto increment pas lors de la génération d'un nouveau jey de donnée.

        $creee = new Etat(1);
        $creee->setLibelle("Créée"); 
        $manager->persist($creee);

        $ouverte = new Etat(2);
        $ouverte->setLibelle("Ouverte");
        $manager->persist($ouverte); 

        $cloturee = new Etat(3);
        $cloturee->setLibelle("Clôturée");
        $manager->persist($cloturee); 

        $activiteEnCours = new Etat(4);
        $activiteEnCours->setLibelle("En cours");
        $manager->persist($activiteEnCours); 

        $passee = new Etat(5);
        $passee->setLibelle("Passée");
        $manager->persist($passee);

        $historisee = new Etat(6);
        $historisee->setLibelle("Historisée");
        $manager->persist($historisee); 

        $annulee = new Etat(7);
        $annulee->setLibelle("Annulée");
        $manager->persist($annulee); 

    $manager->flush();
        
    }
}
