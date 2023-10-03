<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Etat;

class EtatFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group10', 'DBvide'];
    }

    public function load(ObjectManager $manager): void
    {
        $creee = new Etat();
        $creee->setLibelle("ouverte"); 

        $ouverte = new Etat();
        $ouverte->setLibelle("ouverte"); 

        $cloturee = new Etat();
        $cloturee->setLibelle("cloturee"); 

        $activiteEnCours = new Etat();
        $activiteEnCours->setLibelle("activiteEnCours"); 

        $passee = new Etat();
        $passee->setLibelle("passee"); 

        $annulee = new Etat();
        $annulee->setLibelle("annulee"); 

        $historisee = new Etat();
        $historisee->setLibelle("historisee"); 

    $manager->flush();
        
    }
}
