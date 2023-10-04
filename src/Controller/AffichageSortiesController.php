<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RechercheSortiesType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SortieRepository;
use phpDocumentor\Reflection\Types\Boolean;

class AffichageSortiesController extends AbstractController
{
    #[Route('/sorties', name: 'app_affichage_sorties')]
    public function index(Request $request, SortieRepository $sortiesRepository): Response
    {
        $rechercheForm = $this->createForm(RechercheSortiesType::class);
        $rechercheForm->handleRequest($request);

        if ($rechercheForm->isSubmitted() && $rechercheForm->isValid()) {
            $sorties = $sortiesRepository->findWithForm($rechercheForm, $this->getUser());
        }else {
            $sorties = $sortiesRepository->findAll();
        }

        return $this->render('affichage_sorties/index.html.twig', [
            'controller_name' => 'AffichageSortiesController',
            "rechercheForm" => $rechercheForm->createView(),
            "sorties" => $sorties
        ]);
    }
}
