<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\NouveauLieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LieuController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

//    #[Route('/lieu/{id}', name: 'app_get_lieu')]
//    public function getLieu(int $id): JsonResponse
//    {
//        $lieuRepository = $this->entityManager->getRepository(Lieu::class);
//        $lieu = $lieuRepository->find($id);
//
//        if (!$lieu) {
//            return new JsonResponse(['error' => 'Lieu non trouvé'], JsonResponse::HTTP_NOT_FOUND);
//        }
//
//        $data = [
//            'rue' => $lieu->getRue(),
//            'latitude' => $lieu->getLatitude(),
//            'longitude' => $lieu->getLongitude(),
//        ];
//
//        return $this->json($data);
//    }

	#[Route('/get-detail-lieu/{id}', name: 'get-detail-lieu', methods: ['GET'])]
	public function getDetailLieu(Lieu $lieu, EntityManagerInterface $entityManager): JsonResponse
	{

		if (!$lieu) {
			return new JsonResponse(['error' => 'Lieu non trouvée'], 404);
		}

		// Renvoyez le code postal au format JSON
		return new JsonResponse([
			'rue' => $lieu->getRue(),
			'latitude' => $lieu->getLatitude(),
			'longitude' => $lieu->getLongitude(),
		]);
	}

}
