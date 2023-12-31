<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\Entity\Sortie;
use App\Entity\Utilisateur;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findOneBySomeField($value): ?Sortie
   {
       return $this->createQueryBuilder('s')
           ->andWhere('s.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function defaultSearch($utilisateurConnecte ){
    $request = $this->createQueryBuilder('s');

    //n'afficher de base aucune sortie en id = 1 (Créée) a pars celle dont je suis l'organisateur
    $request->andWhere(
        $request->expr()->orX(
            $request->expr()->neq('s.etat', 1), //id = 1 -> Créée
            $request->expr()->eq('s.organisateur', ':utilisateurConnecte')
        )
    )
    ->setParameter('utilisateurConnecte', $utilisateurConnecte);

    $request->orderBy('s.dateHeureDebut', 'DESC');

    return $request->getQuery()->getResult();
   }

    public function findWithForm($form, $utilisateurConnecte )
    {

        $request = $this->createQueryBuilder('s');

        //n'afficher de base aucune sortie en id = 1 (Créée) a pars celle dont je suis l'organisateur
        $request->andWhere(
            $request->expr()->orX(
                $request->expr()->neq('s.etat', 1), //id = 1 -> Créée
                $request->expr()->eq('s.organisateur', ':utilisateurConnecte')
            )
        )
        ->setParameter('utilisateurConnecte', $utilisateurConnecte);

        // Filtrer par campus
        if ($form->get('campus')->getData() != null) {
            $request->andWhere('s.campus  = :idCampus')
            ->setParameter('idCampus', $form->get('campus')->getData()->getId());
        }


        // Filtrer par recherche de nom
        if ($form->get('nom')->getData() != null) {
            $request->andWhere('s.nom LIKE :nom')
            ->setParameter('nom', '%' . $form->get('nom')->getData() . '%');
        }

        // Filtrer par date de début si elle est renseignée
        if ($form->get('dateDebut')->getData() !== null) {
            $request->andWhere('s.dateHeureDebut >= :dateDebut')
            ->setParameter('dateDebut', $form->get('dateDebut')->getData());;
        }

        // Filtrer par date de début si elle est renseignée
        if ($form->get('dateFin')->getData() !== null) {
            $request->andWhere('s.dateHeureDebut <= :dateFin')
            ->setParameter('dateFin', $form->get('dateFin')->getData());
        }

        // Filtrer des sorties dont je suis l'organisateur
        if ($form->get('organisateur')->getData() == true) {
            $request->andWhere('s.organisateur = :val')
            ->setParameter('val', $utilisateurConnecte->getId());
        }

        //member of
        // Filtrer des sorties ou je suis inscrit
        if ($form->get('inscrit')->getData() == true) {
            $request->andwhere(':participant MEMBER OF s.participants')
            ->setParameter('participant', $utilisateurConnecte);
        }

        // Filtrer des sorties ou je ne suis pas inscrit
        if ($form->get('nonInscrit')->getData() == true) {
            $request->andwhere(':participant NOT MEMBER OF s.participants')
            ->setParameter('participant', $utilisateurConnecte);
        }

        // Filtrer par sortie historisée
        if ($form->get('sortiesPassees')->getData() == true) {
            $request->andWhere('s.etat = :val')
            ->setParameter('val', 6); //id = 6 -> historisée
        }

        $request->orderBy('s.dateHeureDebut', 'DESC');

        return $request->getQuery()->getResult();
    }

    public function findSortieWithLieu(int $sortieId)
    {
        return $this->createQueryBuilder('s')
        ->select('s', 'l')
        ->leftJoin('s.lieu', 'l')
        ->where('s.id = :sortieId')
        ->setParameter('sortieId', $sortieId)
        ->getQuery()
        ->getOneOrNullResult()
        ;

    }
}
