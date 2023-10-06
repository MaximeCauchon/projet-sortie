<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\ModifSortieType;
use App\Form\AnnulerSortieType;
use App\Form\NouvelleSortieType;
use App\Repository\EtatRepository;
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
        $etatRepo = $entityManager->getRepository(Etat::class);
        $sortie->setEtat($etatRepo->find(1));
        $campus = $this->getUser()->getCampus();
        $sortie->setCampus($campus);

        $nouvelleSortieForm = $this->createForm(NouvelleSortieType::class, $sortie);
        $nouvelleSortieForm->handleRequest($request);



        if ($nouvelleSortieForm->isSubmitted() && $nouvelleSortieForm->isValid()) {

            if ($nouvelleSortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatRepo->find(2));
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
			$this->addFlash('success', 'La sortie a été créée.');
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
        $sortie = $sortieRepository->findSortieWithLieu($id);

        if (!$sortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas.");
        }

        $etatRepo = $entityManager->getRepository(Etat::class);

        $modifSortieForm = $this->createForm(ModifSortieType::class, $sortie);
        $modifSortieForm->handleRequest($request);

        if ($modifSortieForm->isSubmitted() && $modifSortieForm->isValid()) {

            if ($modifSortieForm->get('supprimer')->isClicked()) {
                $sortie->supprSortie();
            }

            if ($modifSortieForm->get('publier')->isClicked()) {
                $sortie->publierSortie();
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

    #[Route('/supprimer-sortie/{id}', name: 'supprimer_sortie')]
    public function supprSortie(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($sortie);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('app_affichage_sorties'));
    }

    #[Route('/annuler-sortie/{id}', name: 'annuler_sortie')]
    public function annulSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request): Response
    {   

        $sortie = $sortieRepository->findSortieWithLieu($id);

        if (!$sortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas.");
        }

        $etatRepo = $entityManager->getRepository(Etat::class);

        $annulSortieForm = $this->createForm(AnnulerSortieType::class, $sortie);
        $annulSortieForm->handleRequest($request);

        if ($annulSortieForm->isSubmitted() && $annulSortieForm->isValid()) {

            $sortie->setEtat($etatRepo->find(7));
            $entityManager->flush();

            return $this->redirect($this->generateUrl('app_affichage_sorties'));
        }

        return $this->render('sortie/annuler-sortie.html.twig', [
            'controller_name' => 'SortieController',
            'sortie' => $sortie,
            'annulSortieForm' => $annulSortieForm->createView()
        ]);
    }

    #[Route('/publier-sortie/{id}', name: 'publier_sortie')]
    public function publierSortie(Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepo): Response
    {
        $sortie->setEtat($etatRepo->find(2));
        $entityManager->flush();

        return $this->redirect($this->generateUrl('app_affichage_sorties'));
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
