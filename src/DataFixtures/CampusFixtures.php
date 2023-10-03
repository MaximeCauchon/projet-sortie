<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Campus;

class CampusFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group10', 'DBvide'];
    }

    public function load(ObjectManager $manager): void
    {
        $nantes = new Campus();
        $nantes->setNom("Nantes"); 

        $rennes = new Campus();
        $rennes->setNom("Rennes"); 

        $quimper = new Campus();
        $quimper->setNom("Quimper"); 

        $niort = new Campus();
        $niort->setNom("Niort"); 

    $manager->flush();
        
    }
}
