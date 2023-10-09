<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Sortie;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;


class NouvelleSortieType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('nom', TextType::class, [
				'label' => 'Nom de la sortie :'
			])
			->add('dateHeureDebut', DateTimeType::class, [
				'html5' => true,
				'data' => new \DateTime('now'),
				'widget' => 'single_text',
				'label' => 'Date et heure de la sortie :'
			])
			->add('dateLimiteInscription', DateTimeType::class, [
				'html5' => true,
				'data' => new \DateTime('now'),
				'widget' => 'single_text',
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
				'with_hours' => true,
				'hours' => range(0, 8),
				'with_minutes' => true,
				'labels' => [
					'minutes' => "minutes",
					'hours' => "heures",
				],
			])
			->add('infosSortie', TextType::class, [
				'required' => false,
				'label' => 'Descriptions et infos :'
			])
			->add('ville', EntityType::class, [
				'label' => 'Ville :',
				'class' => Ville::class,
				'choice_label' => 'nom',
				'placeholder' => '-- Sélectionner une ville --',
				'mapped' => false
			])

			->add('lieu', ChoiceType::class, [
				'label' => 'Lieu :',
				'choice_label' => 'nom',
				'placeholder' => "-- Sélectionner d'abord une ville --",
			])

			->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
			->add('publier', SubmitType::class, ['label' => 'Publier']);

		$builder->get('ville')->addEventListener(
			FormEvents::POST_SUBMIT,
			function (FormEvent $event) {
				$form = $event->getForm();
				$form->getParent()->add('lieu', EntityType::class, [
					'class' => Lieu::class,
					'label' => 'Lieu :',
					'choice_label' => 'nom',
					'placeholder' => '-- Sélectionner un lieu --',
					'choices' => $form->getData()->getLieux()
				]);
			}
		);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Sortie::class,
		]);
	}
}
