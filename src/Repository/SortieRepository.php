<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

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

    public function findWithForm($form, $utilisateurConnecte): Sortie
    {
        dd($utilisateurConnecte);

        $request = $this->createQueryBuilder('s');

        // $request->andWhere('s.campus_id  = :val')
        //     ->setParameter('val', $form.viewData.campus.id);

        // if ($form.viewData.nom != null) {
        //     $request->andWhere('s.exampleField = :val')
        //     ->setParameter('val', $form.viewData.nom);
        // }

        // if ($form.viewData.dateDebut != null) {
        //     $request->andWhere('s.exampleField = :val')
        //     ->setParameter('val', $form.viewData.dateDebut);
        // }

        // if ($form.viewData.dateFin != null) {
        //     $request->andWhere('s.exampleField = :val')
        //     ->setParameter('val', $form.viewData.dateFin);
        // }

        // if ($form.viewData.organisateur != false) {
        //     $request->andWhere('s.organisateur_id = :val')
        //     ->setParameter('val', $utilisateurConnecte.organise);
        // }

        // if ($form.viewData.inscrit != false) {
        //     $request->andWhere('s.exampleField = :val')
        //     ->setParameter('val', $form.viewData.inscrit);
        // }

        // if ($form.viewData.nonInscrit != false) {
        //     $request->andWhere('s.exampleField = :val')
        //     ->setParameter('val', $form.viewData.nonInscrit);
        // }

        // if ($form.viewData.sortiesPassees != false) {
        //     $request->andWhere('s.exampleField = :val')
        //     ->setParameter('val', $form.viewData.sortiesPassees);
        // }


        // ->setMaxResults(10)

        $request->getQuery()
        ->getResult();        ;
    }
}
