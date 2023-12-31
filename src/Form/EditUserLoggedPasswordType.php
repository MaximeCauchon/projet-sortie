<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserLoggedPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('plainPassword', RepeatedType::class, [
				// instead of being set onto the object directly,
				// this is read and encoded in the controller
				'type' => PasswordType::class,
				'invalid_message' => 'Les mots de passes doivent correspondre',
				'required' => true,
				'first_options'  => ['label' => 'Mot de Passe'],
				'second_options' => ['label' => 'Répétez le mot de passe'],
				'mapped' => false,
				'attr' => ['autocomplete' => 'new-password'],
				'constraints' => [
					new NotBlank([
						'message' => 'Entrez un nouveau mot de passe',
					]),
					new Length([
						'min' => 6,
						'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
						// max length allowed by Symfony for security reasons
						'max' => 4096,
					]),
				],
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
