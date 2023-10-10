<?php

namespace App\Controller;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VilleController extends AbstractController
{
    #[Route('/get-cp-by-ville/{id}', name: 'get_cp_by_ville', methods: ['GET'])]
    public function getCodePostalByVille($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $villeRepository = $entityManager->getRepository(Ville::class);

        // Récupérez la ville par son ID
        $ville = $villeRepository->find($id);

        if (!$ville) {
            return new JsonResponse(['error' => 'Ville non trouvée'], 404);
        }

        // Renvoyez le code postal au format JSON
        return new JsonResponse(['code_postal' => $ville->getCodePostal()]);
    }
}
