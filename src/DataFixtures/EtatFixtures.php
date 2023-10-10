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
        $creee->setLibelle("ouverte"); 
        $manager->persist($creee);

        $ouverte = new Etat();
        $ouverte->setLibelle("ouverte");
        $manager->persist($ouverte); 

        $cloturee = new Etat();
        $cloturee->setLibelle("cloturee");
        $manager->persist($cloturee); 

        $activiteEnCours = new Etat();
        $activiteEnCours->setLibelle("activiteEnCours");
        $manager->persist($activiteEnCours); 

        $passee = new Etat();
        $passee->setLibelle("passee");
        $manager->persist($passee); 

        $annulee = new Etat();
        $annulee->setLibelle("annulee");
        $manager->persist($annulee); 

        $historisee = new Etat();
        $historisee->setLibelle("historisee");
        $manager->persist($historisee); 

    $manager->flush();
        
    }
}
