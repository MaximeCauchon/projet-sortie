<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Sortie;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class ModifSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $sortie = $event->getData();
                $lieu = $sortie->getLieu();

                $ville = $lieu->getVille()->getNom();
                $event->getForm()->add('ville', EntityType::class, [
                    'label' => 'Ville :',
                    'class' => Ville::class,
                    'choice_label' => 'nom',
                    'placeholder' => $ville,
                    'mapped' => false,
                ]);
            })

            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
				'html5' => true,
				'date_widget' => 'single_text',
				'label' => 'Date et heure de la sortie :'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
				'html5' => true,
				'date_widget' => 'single_text',
				'label' => "Date limite d'inscription :"
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
				'required' => false,
				'label' => 'Nombre de places :',
				'attr' => [
					'min' => 1,
					'minMessage' => 'Ce chiffre ne peut être négatif ou egal à 0. Si vous ne souhaitez pas mettre de limite, ne remplissez pas le champ.',
				]
            ])
            ->add('duree', DateIntervalType::class, [
				'label' => 'Durée :',
				'with_years' => false,
				'with_months' => false,
				'with_days' => false,
//				'days' => range(0,30),
				'with_hours' => false,
//				'hours' => range(0,24),
				'with_minutes' => true,
				'minutes' => range(0, 120),
				'labels' => [
					'minutes' => "minutes",
//					'days' => "jours",
//					'hours' => "heures",
				]
            ])
            ->add('infosSortie', TextType::class, [
				'required' => false,
				'label' => 'Descriptions et infos :'
            ])
            
            ->add('lieu', EntityType::class, [
				'class' => Lieu::class,
				'label' => 'Lieu :',
				'choice_label' => 'nom',
				'placeholder' => '',
            ])
            ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publier', SubmitType::class, ['label' => 'Publier'])
            ->add('supprimer', SubmitType::class, ['label' => 'Supprimer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
