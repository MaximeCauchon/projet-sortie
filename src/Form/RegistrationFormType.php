<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('email', EmailType::class)
			->add('plainPassword', PasswordType::class, [
				// instead of being set onto the object directly,
				// this is read and encoded in the controller
				'mapped' => false,
				'attr' => ['autocomplete' => 'new-password'],
				'constraints' => [
					new NotBlank([
						'message' => 'Veuillez entrer un mot de passe.',
					]),
					new Length([
						'min' => 6,
						'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
						// max length allowed by Symfony for security reasons
						'max' => 4096,
					]),
				],
			])
			->add('pseudo', TextType::class)
			->add('nom', TextType::class)
			->add('prenom', TextType::class)
			->add('telephone', TextType::class, [
				'required'=>false,
			])
			->add('isActif', CheckboxType::class)
//			->add('image', TextType::class, [
//				'required'=>false,
//			])
			->add('campus', EntityType::class, [
				'class' => Campus::class,
				'choice_label' => 'nom',

			]);
//			->add('agreeTerms', CheckboxType::class, [
//				'mapped' => false,
//				'constraints' => [
//					new IsTrue([
//						'message' => 'You should agree to our terms.',
//					]),
//				],
//			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Participant::class,
		]);
	}
}
