<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\ModifSortieType;
use App\Form\NouveauLieuType;
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

		$lieu = new Lieu();

		$nouveauLieuForm = $this->createForm(NouveauLieuType::class, $lieu);
		$nouveauLieuForm->handleRequest($request);

		if ($nouveauLieuForm->isSubmitted() && $nouveauLieuForm->isValid()) {
			$entityManager->persist($lieu);
			$entityManager->flush();
			$this->addFlash('success', 'Le lieu a été créé.');
		}

		return $this->render('sortie/nouvelle-sortie.html.twig', [
			'controller_name' => 'SortieController',
			'nouvelleSortieForm' => $nouvelleSortieForm->createView(),
			'nouveauLieuForm' => $nouveauLieuForm->createView()
		]);
	}

	#[Route('/modifier-sortie/{id}', name: 'modifier_sortie')]
	public function modifSortie(int $id, SortieRepository $sortieRepository, Request $request, EntityManagerInterface $entityManager): Response
	{
		$sortie = $sortieRepository->findSortieWithLieu($id);

		if (!$sortie) {
			$this->addFlash('warning', 'Cette sortie n\'existe pas.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$user = $this->getUser();
		if (!$user->participantOrganisateurSortie($sortie)) {
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à modifier cette sortie.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$modifSortieForm = $this->createForm(ModifSortieType::class, $sortie);
		$modifSortieForm->handleRequest($request);

		if ($modifSortieForm->isSubmitted() && $modifSortieForm->isValid()) {

            if ($modifSortieForm->get('supprimer')->isClicked()) {
                return $this->redirectToRoute('supprimer_sortie', ['id' => $sortie->getId()]);
            }

            if ($modifSortieForm->get('publier')->isClicked()) {
                return $this->redirectToRoute('publier_sortie', ['id' => $sortie->getId()]);
            }
            $entityManager->flush();

			$this->addFlash('success', 'Sortie modifiée !');
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
		if (!$sortie) {
			$this->addFlash('warning', 'Cette sortie n\'existe pas.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$user = $this->getUser();
		if (!$user->participantOrganisateurSortie($sortie) || !$sortie->getEtat()->getId() == 1) {
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à supprimer cette sortie.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$entityManager->remove($sortie);
		$entityManager->flush();

		$this->addFlash('alert', 'Sortie supprimée !');
		return $this->redirect($this->generateUrl('app_affichage_sorties'));
	}

	#[Route('/annuler-sortie/{id}', name: 'annuler_sortie')]
	public function annulSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request): Response
	{
		$sortie = $sortieRepository->findSortieWithLieu($id);
		if (!$sortie) {
			$this->addFlash('warning', 'Cette sortie n\'existe pas.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$user = $this->getUser();
		if (in_array($sortie->getEtat()->getId(), [2, 3])
			&& ($user->participantOrganisateurSortie($sortie)
				|| $this->isGranted("ROLE_ADMIN"))) {
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à annuler cette sortie.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$etatRepo = $entityManager->getRepository(Etat::class);

		$annulSortieForm = $this->createForm(AnnulerSortieType::class, $sortie);
		$annulSortieForm->handleRequest($request);

		if ($annulSortieForm->isSubmitted() && $annulSortieForm->isValid()) {

			$sortie->setEtat($etatRepo->find(7));
			$entityManager->flush();


			$this->addFlash('success', 'Sortie annulée !');
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

		$user = $this->getUser();
		if (in_array($sortie->getEtat()->getId(), [1])
			&& $user->participantOrganisateurSortie($sortie)) {
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à publier cette sortie.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		$sortie->setEtat($etatRepo->find(2));
		$entityManager->flush();

		$this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' ouverte aux inscriptions !');
		return $this->redirect($this->generateUrl('app_affichage_sorties'));
	}

	#[Route('/details-sortie/{id}', name: 'details_sortie')]
	public function showSortie(int $id, SortieRepository $sortieRepository): Response
	{
		$sortie = $sortieRepository->find($id);
		if (!$sortie) {
			$this->addFlash('warning', 'Cette sortie n\'existe pas.');
			return $this->redirectToRoute('app_affichage_sorties');
		}

		if ($sortie->getEtat()->getId() !== 6) {
			return $this->render('sortie/details-sortie.html.twig', [
				'controller_name' => 'SortieController',
				'sortie' => $sortie
			]);
		} else {
			$this->addFlash(
				'alert',
				"Cette sortie est archivée et n'est plus consultable !"
			);
			return $this->redirect($this->generateUrl('app_affichage_sorties'));
		}

	}
}
