<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\RegistrationFormType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
	#[Route('/gestionParticipants', name: 'gestionParticipants')]
	public function gestionParticipants(): Response
	{
		return $this->render('admin/gestionParticipants.html.twig', [
			'controller_name' => 'AdminController',
		]);
	}

	#[Route('/gestionVilles', name: 'gestionVilles')]
	public function gestionVilles(Request $request, VilleRepository $villeRepo, EntityManagerInterface $entityManager): Response
	{
		$newVille = new Ville();
		$newVilleForm = $this->createForm(VilleType::class, $newVille);
		$newVilleForm->handleRequest($request);
		if ($newVilleForm->isSubmitted() && $newVilleForm->isValid()) {
			$entityManager->persist($newVille);
			$entityManager->flush();
		}
		$newVille = new Ville();
		$newVilleForm = $this->createForm(VilleType::class, $newVille);
		$villes = $villeRepo->findBy([], ['nom' => 'ASC']);

		return $this->render('admin/gestionVilles.html.twig', [
			'villes' => $villes,
			'newVilleForm' => $newVilleForm,
			'maVilleAModifier' => null,
			'editVilleForm' => null
		]);
	}

	#[Route(path: '/gestionVilles/edit/{id}', name: 'gestionVilles_Edit')]
	public function gestionVilles_Edit(Ville $maVilleAModifier, Request $request, VilleRepository $villeRepo, EntityManagerInterface $entityManager): Response
	{
		$villes = $villeRepo->findBy([], ['nom' => 'ASC']);
		$editVilleForm = $this->createForm(VilleType::class, $maVilleAModifier);
		$editVilleForm->handleRequest($request);
		if ($editVilleForm->isSubmitted() && $editVilleForm->isValid()) {
			$entityManager->persist($maVilleAModifier);
			$entityManager->flush();
			$this->addFlash('success', 'La ville ' . $maVilleAModifier->getNom() . ' a bien été modifiée.');
			return $this->redirectToRoute('admin_gestionVilles');
		}
		return $this->render('admin/gestionVilles.html.twig', [
			'villes' => $villes,
			'newVilleForm' => null,
			'maVilleAModifier' => $maVilleAModifier,
			'editVilleForm' => $editVilleForm
		]);
	}

	#[Route(path: '/gestionVilles/supp/{id}', name: 'gestionVilles_Supp')]
	public function gestionVilles_Supp(Ville $maVilleASupprimer, Request $request, VilleRepository $villeRepo, EntityManagerInterface $entityManager)
	{
		if ($maVilleASupprimer->getLieux()->count() > 0) {
			$this->addFlash('warning', 'La ville ' . $maVilleASupprimer->getNom() . ' ne peut être supprimée car des lieux y sont associés (' . $maVilleASupprimer->getLieux()->count() . ').');
		} else {
			$entityManager->remove($maVilleASupprimer);
			$entityManager->flush();
			$this->addFlash('success', 'La ville ' . $maVilleASupprimer->getNom() . ' a bien été supprimée.');
		}
		return $this->redirectToRoute('admin_gestionVilles');
	}

	#[Route('/gestionCampus', name: 'gestionCampus')]
	public function gestionCampus(): Response
	{
		return $this->render('admin/gestionCampus.html.twig', [
			'controller_name' => 'AdminController',
		]);
	}
}
