<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Participant;
use App\Form\EditUserLoggedType;
use App\Form\EditUserLoggedPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/', name: 'participant_')]

class ParticipantController extends AbstractController
{
	#[Route(path: 'participant/{id}', name: 'details', requirements: ['page' => '\d+'])]
	public function details(Participant $part): Response
	{
		return $this->render('participant/details.html.twig', [
			'participant' => $part,
		]);
	}

	#[Route(path: '/edition-profil', name: 'editLoggedUser')]
	public function editLoggedUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
	{
		$user = $this->getUser();
		$formEditLoggedUser = $this->createForm(EditUserLoggedType::class, $user);
		$formEditLoggedUser->handleRequest($request);

		$formEditLoggedUserPassword = $this->createForm(EditUserLoggedPasswordType::class, $user);
		$formEditLoggedUserPassword->handleRequest($request);

		if ($formEditLoggedUser->isSubmitted() && $formEditLoggedUser->isValid()) {

			$imageFile = $formEditLoggedUser->get('imageFile')->getData();

			if ($imageFile) {
				$originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
				// this is needed to safely include the file name as part of the URL
				$safeFilename = $slugger->slug($originalFilename);
				$newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

				try {
					$imageFile->move(
						$this->getParameter('avatar_directory'),
						$newFilename
					);
				} catch (FileException $e) {
					$this->addFlash('error', 'Un problème est survenue lors du chargement de votre image.');
					$this->addFlash('error', $e->getMessage());
				}

				// Before changing the filepath, we delete the old file
				$filename = $user->getImage();
				if ($filename) {
					$fileRoute = $this->getParameter("avatar_directory").$filename;
					$filesystem = new Filesystem();
					$filesystem->remove($fileRoute);
				}

				$user->setImage($newFilename);

			}

			$entityManager->persist($user);
			$entityManager->flush();

			$this->addFlash('success', 'Votre profil a été modifié');

		}

		if ($formEditLoggedUserPassword->isSubmitted() && $formEditLoggedUserPassword->isValid()) {

			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$formEditLoggedUserPassword->get('plainPassword')->getData()
				)
			);
			$entityManager->persist($user);
			$entityManager->flush();

			$this->addFlash('success', 'Votre mot de passe a été modifié');

		}
		return $this->render('participant/editLoggedUser.html.twig', [
			'editLoggedUserForm' => $formEditLoggedUser->createView(),
			'editLoggedUserPasswordForm' => $formEditLoggedUserPassword->createView(),
		]);
	}

	#[Route(path: 'inscription/{id}', name: 'inscription')]
	public function addInscription( Sortie $sortie, EntityManagerInterface $entityManager): RedirectResponse
	{
		$participant = $this->getUser();

		if(!$participant->participantInscritSortie($sortie) && $sortie->getEtat()->getId()==2) {
			$sortie->addParticipant($participant);
			$entityManager->persist($sortie);
            $entityManager->flush();
		}
		$this->addFlash('success', 'Vous êtes inscrit à la sortie.');
		return $this->redirectToRoute('details_sortie', ['id' => $sortie->getId()]);

	}

	#[Route(path: 'desistement/{id}', name: 'desistement')]
	public function removeInscription(Sortie $sortie, EntityManagerInterface $entityManager): RedirectResponse
	{
		$participant = $this->getUser();

		if($participant->participantInscritSortie($sortie)) {
			$sortie->removeParticipant($participant);
			$entityManager->persist($sortie);
            $entityManager->flush();
		}
		$this->addFlash('success', 'Vous êtes désinscrit de la sortie.');
		return $this->redirectToRoute('details_sortie', ['id' => $sortie->getId()]);

	}


}
