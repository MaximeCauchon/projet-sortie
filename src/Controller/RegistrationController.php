<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin', name: 'admin_')]
class RegistrationController extends AbstractController
{
	#[Route('/admin/register', name: 'register')]
	public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
	{
		$user = new Participant();
		$user->setIsActif(true);

		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);

			$entityManager->persist($user);
			$entityManager->flush();
			// do anything else you need here, like send an email

			//Si on veut s'authentifier imédiatement après avoir créé l'utilisateur
//            return $userAuthenticator->authenticateUser(
//                $user,
//                $authenticator,
//                $request
//            );
			$newUser = new Participant();
			$newUser->setIsActif(true);

			$form = $this->createForm(RegistrationFormType::class, $newUser);
			$this->addFlash('success', 'Le participant ' . $user->getPseudo() . 'a été créé.');

			return $this->render('registration/register.html.twig', [
				'registrationForm' => $form->createView(),
			]);
		}

		return $this->render('registration/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}
}
