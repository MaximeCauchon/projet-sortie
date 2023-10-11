<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RechercheSortiesType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SortieRepository;

class AffichageSortiesController extends AbstractController
{
    #[Route('/', name: 'app_affichage_sorties')]
    public function index(Request $request, SortieRepository $sortiesRepository): Response
    {
        $rechercheForm = $this->createForm(RechercheSortiesType::class);
        $rechercheForm->handleRequest($request);
        if ($rechercheForm->isSubmitted() && $rechercheForm->isValid()) {
            $sorties = $sortiesRepository->findWithForm($rechercheForm, $this->getUser());
        }
        elseif(!$rechercheForm->isSubmitted()) {
            $sorties = $sortiesRepository->defaultSearch($this->getUser());

        }else{
            $sorties = $sortiesRepository->defaultSearch($this->getUser());
            $this->addFlash(
                'Alert',
                'Le formulaire n\'est pas valide, pas de tris appliqué'
             );
        }

        return $this->render('affichage_sorties/index.html.twig', [
            'controller_name' => 'AffichageSortiesController',
            "rechercheForm" => $rechercheForm->createView(),
            "sorties" => $sorties
        ]);
    }
}
