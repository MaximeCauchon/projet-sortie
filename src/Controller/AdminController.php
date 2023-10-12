<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\RegistrationFormType;
use App\Form\UploadParticipantViaCsvType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
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
	// ------------------- Gestion Villes -------------------
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


// ------------------- Gestion Participants -------------------
	#[Route('/gestionParticipants', name: 'gestionParticipants')]
	public function gestionParticipants(ParticipantRepository $partRepo, Request $request, EntityManagerInterface $em): Response
	{

		$uploadParticipantViaCsvForm = $this->createForm(UploadParticipantViaCsvType::class);
		$uploadParticipantViaCsvForm->handleRequest($request);
		if ($uploadParticipantViaCsvForm->isSubmitted() && $uploadParticipantViaCsvForm->isValid()) {


			$file = $uploadParticipantViaCsvForm->get('filePart')->getData();
			$campus = $uploadParticipantViaCsvForm->get('campus')->getData();
			$count = 0;
			try {
				if (($handle = fopen($file->getPathname(), "r")) !== false) {
					// Read and process the lines.
					// Skip the first line if the file includes a header
					while (($data = fgetcsv($handle)) !== false) {
						if ($count == 0) {
							$nameCol = $data;
							dump($nameCol);
						} else {
							// Do the processing: Map line to entity, validate if needed
							$part = new Participant();
							$part->setCampus($campus);
							$part->setRoles([]);
							$part->setIsActif(true);

							$part->setNom($data[array_search('nom', $nameCol)]);
							$part->setPrenom($data[array_search('prenom', $nameCol)]);
							$part->setPseudo($data[array_search('pseudo', $nameCol)]);
							$part->setEmail($data[array_search('email', $nameCol)]);
							$part->setTelephone($data[array_search('telephone', $nameCol)]);
							$part->setPassword($data[array_search('password', $nameCol)]);
							// Assign fields
							$em->persist($part);
						}
						$count++;
					}
					fclose($handle);
					$em->flush();
				}
				$this->addFlash('success', $count - 1 . " participants ont été ajoutés dans le campus " . $campus->getNom() . ".");
			} catch (\Exception $e) {
				if ($e->getCode() == 1062) {
					$this->addFlash('error', "Une erreur s'est produite : " . $e->getPrevious()->getPrevious()->errorInfo[2]);
				} else {
					$this->addFlash('error', "Une erreur s'est produite : " . $e->getMessage());
				}
			}


		}
		$participants = $partRepo->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']);

		return $this->render('admin/gestionParticipants.html.twig', [
			'participants' => $participants,
			'uploadParticipantViaCsvForm' => $uploadParticipantViaCsvForm,
		]);
	}

	#[Route(path: '/gestionParticipants/isActif/{id}', name: 'gestionParticipants_isActif')]
	public function gestionParticipants_isActif(Participant $p, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepo)
	{
		if ($this->getUser()->getUserIdentifier() == $p->getUserIdentifier()){
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à vous désactiver vous-même.');
			return $this->redirectToRoute('admin_gestionParticipants');
		}

		if ($p->isIsActif()) {
			$message = $p->desinscriptionDeTousLesEvenementsFuturs();
			if ($message != "") {
				$this->addFlash('success', $message);
			}
			$message2 = $p->annulationDesEvenementsOrganisateur($etatRepo);
			if ($message2 != "") {
				$this->addFlash('success', $message2);
			}
		}
		$p->setIsActif(!$p->isIsActif());
		$entityManager->persist($p);
		$entityManager->flush();
		$str = $p->isIsActif() ? 'actif' : 'inactif';
		$this->addFlash('success', 'Le participant ' . $p->getPseudo() . ' est devenu ' . $str . '.');
		return $this->redirectToRoute('admin_gestionParticipants');
	}

	#[Route(path: '/gestionParticipants/isAdmin/{id}', name: 'gestionParticipants_isAdmin')]
	public function gestionParticipants_isAdmin(Participant $p, Request $request, EntityManagerInterface $entityManager)
	{
		if ($this->getUser()->getUserIdentifier() == $p->getUserIdentifier()){
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à vous retirer vos propre droits administrateur.');
			return $this->redirectToRoute('admin_gestionParticipants');
		}
		if ($p->isAdmin()) {
//			On retire les droit d'administration
			$p->setRoles([]);

		} else {
//			On donne les droits d'administration
			$p->setRoles(['ROLE_ADMIN']);
		}

		$entityManager->persist($p);
		$entityManager->flush();
		$str = $p->isIsActif() ? 'est devenu administrateur.' : 'n\'est plus administrateur.';
		$this->addFlash('success', 'Le participant ' . $p->getPseudo() . $str);

		return $this->redirectToRoute('admin_gestionParticipants');
	}

	#[Route(path: '/gestionParticipants/supp/{id}', name: 'gestionParticipants_supp')]
	public function gestionParticipants_supp(Participant $p, ParticipantRepository $partRepo, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepo)
	{
		if ($this->getUser()->getUserIdentifier() == $p->getUserIdentifier()){
			$this->addFlash('warning', 'Vous n\'êtes pas autorisé à supprimer votre propre compte car vous êtes administrateur.');
			return $this->redirectToRoute('admin_gestionParticipants');
		}

		$message = $p->desinscriptionDeTousLesEvenementsFuturs();
		if ($message != "") {
			$this->addFlash('success', $message);
		}

		$message2 = $p->annulationDesEvenementsOrganisateur($etatRepo);
		if ($message2 != "") {
			$this->addFlash('success', $message2);
		}

		$entityManager->persist($p);
		$entityManager->flush();
		$entityManager->remove($p);
		$entityManager->flush();
		$this->addFlash('success', 'Le participant ' . $p->getPseudo() . "a été supprimé.");
		return $this->redirectToRoute('admin_gestionParticipants');
	}

// ------------------- Gestion Campus -------------------

	#[Route('/gestionCampus', name: 'gestionCampus')]
	public function gestionCampus(Request $request, CampusRepository $campusRepo, EntityManagerInterface $entityManager): Response
	{
		$newCampus = new Campus();
		$newCampusForm = $this->createForm(CampusType::class, $newCampus);
		$newCampusForm->handleRequest($request);
		if ($newCampusForm->isSubmitted() && $newCampusForm->isValid()) {
			$entityManager->persist($newCampus);
			$entityManager->flush();
		}
		$newCampus = new Campus();
		$newCampusForm = $this->createForm(CampusType::class, $newCampus);
		$campus = $campusRepo->findBy([], ['nom' => 'ASC']);

		return $this->render('admin/gestionCampus.html.twig', [
			'campus' => $campus,
			'newCampusForm' => $newCampusForm,
			'monCampusAModifier' => null,
			'editCampusForm' => null
		]);
	}

	#[Route(path: '/gestionCampus/edit/{id}', name: 'gestionCampus_Edit')]
	public function gestionCampus_Edit(Campus $monCampusAModifier, Request $request, CampusRepository $campusRepo, EntityManagerInterface $entityManager): Response
	{
		$campus = $campusRepo->findBy([], ['nom' => 'ASC']);
		$editCampusForm = $this->createForm(CampusType::class, $monCampusAModifier);

		$editCampusForm->handleRequest($request);
		if ($editCampusForm->isSubmitted() && $editCampusForm->isValid()) {
			$entityManager->persist($monCampusAModifier);
			$entityManager->flush();
			$this->addFlash('success', 'Le campus ' . $monCampusAModifier->getNom() . ' a bien été modifié.');
			return $this->redirectToRoute('admin_gestionCampus');
		}
		return $this->render('admin/gestionCampus.html.twig', [
			'campus' => $campus,
			'newCampusForm' => null,
			'monCampusAModifier' => $monCampusAModifier,
			'editCampusForm' => $editCampusForm
		]);
	}

	#[Route(path: '/gestionCampus/supp/{id}', name: 'gestionCampus_Supp')]
	public function gestionCampus_Supp(Campus $monCampusASupprimer, Request $request, CampusRepository $campusRepo, EntityManagerInterface $entityManager)
	{
		if ($monCampusASupprimer->getParticipants()->count() > 0) {
			$this->addFlash('warning', 'Le campus ' . $monCampusASupprimer->getNom() . ' ne peut être supprimée car des participants y sont associés (' . $monCampusASupprimer->getParticipants()->count() . ').');
		} else {
			$entityManager->remove($monCampusASupprimer);
			$entityManager->flush();
			$this->addFlash('success', 'La campus ' . $monCampusASupprimer->getNom() . ' a bien été supprimé.');
		}
		return $this->redirectToRoute('admin_gestionCampus');
	}
}
