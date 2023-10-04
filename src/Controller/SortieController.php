<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\ModifSortieType;
use App\Form\NouvelleSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SortieController extends AbstractController
{
    #[Route('/nouvelle-sortie', name: 'nouvelle_sortie')]
    public function ajoutSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();


        $currentUser = $this->getUser();
        $sortie->setOrganisateur($currentUser);

        $nouvelleSortieForm = $this->createForm(NouvelleSortieType::class, $sortie);
        $nouvelleSortieForm->handleRequest($request);

        $etatRepo = $entityManager->getRepository(Etat::class);
        $sortie->setEtat($etatRepo->find(1));

        $campus = $this->getUser()->getCampus();
        $sortie->setCampus($campus);

        if ($nouvelleSortieForm->isSubmitted() && $nouvelleSortieForm->isValid()) {

            if ($nouvelleSortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatRepo->find(2));
            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('details_sortie', ['id' => $sortie->getId()]));
        }

        return $this->render('sortie/nouvelle-sortie.html.twig', [
            'controller_name' => 'SortieController',
            'nouvelleSortieForm' => $nouvelleSortieForm->createView()
        ]);
    }

    #[Route('/modifier-sortie/{id}', name: 'modifier_sortie')]
    public function modifSortie(int $id, SortieRepository $sortieRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas.");
        }

        $etatRepo = $entityManager->getRepository(Etat::class);

        $modifSortieForm = $this->createForm(ModifSortieType::class, $sortie);
        $modifSortieForm->handleRequest($request);

        if ($modifSortieForm->isSubmitted() && $modifSortieForm->isValid()) {

            if ($modifSortieForm->get('supprimer')->isClicked()) {
                $entityManager->remove($sortie);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('app_main'));
            }

            if ($modifSortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatRepo->find(2));
            }
            $entityManager->flush();

            return $this->redirect($this->generateUrl('details_sortie', ['id' => $sortie->getId()]));
        }

        return $this->render('sortie/modif-sortie.html.twig', [
            'controller_name' => 'SortieController',
            'sortie' => $sortie,
            'modifSortieForm' => $modifSortieForm->createView()
        ]);
    }

    #[Route('/details-sortie/{id}', name: 'details_sortie')]
    public function showSortie(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas.");
        }

        return $this->render('sortie/details-sortie.html.twig', [
            'controller_name' => 'SortieController',
            'sortie' => $sortie
        ]);
    }
}
