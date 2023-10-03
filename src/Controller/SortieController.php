<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
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

            return $this->redirect($this->generateUrl('details-sortie', ['id' => $sortie->getId()]));
        }

        return $this->render('sortie/nouvelle-sortie.html.twig', [
            'controller_name' => 'SortieController',
            'nouvelleSortieForm' => $nouvelleSortieForm->createView()
        ]);
    }
}
