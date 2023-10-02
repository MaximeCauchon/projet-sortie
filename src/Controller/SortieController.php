<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sorties', name: 'sorties_list')]
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    #[Route('/nouvelle-sortie', name: 'nouvelle_sortie')]
    public function addSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $currentUser = $this->getUser();
        $sortie->setOrganisateur($currentUser);
        $sortieForm = $this->createForm(NouvelleSortieFormType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->


            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'sortie créée');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/nouvelle-sortie.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
