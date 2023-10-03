<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
	public function gestionVilles(): Response
	{
		return $this->render('admin/gestionVilles.html.twig', [
			'controller_name' => 'AdminController',
		]);
	}

	#[Route('/gestionCampus', name: 'gestionCampus')]
	public function gestionCampus(): Response
	{
		return $this->render('admin/gestionCampus.html.twig', [
			'controller_name' => 'AdminController',
		]);
	}
}
