<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Campus;

class CampusFixtures
{


    public function load(ObjectManager $manager): void
    {
        $nantes = new Campus();
        $nantes->setNom("Nantes");
        $manager->persist($nantes); 

        $rennes = new Campus();
        $rennes->setNom("Rennes"); 
        $manager->persist($rennes);

        $quimper = new Campus();
        $quimper->setNom("Quimper");
        $manager->persist($quimper); 

        $niort = new Campus();
        $niort->setNom("Niort"); 
        $manager->persist($niort);

		$manager->flush();
        
    }
}
