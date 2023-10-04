<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class EditUserLoggedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('email', EmailType::class, [
				'label' => 'Email'
			])
			->add('pseudo', TextType::class)
			->add('nom', TextType::class)
			->add('prenom', TextType::class)
			->add('telephone', TextType::class, [
				'required'=>false,
				'label' => 'Téléphone'
			])
            ->add('isActif')
			->add('imageFile', FileType::class, [
				'label' => 'Photo de profil',
				'mapped' => false,
				'required' => false,
				'constraints' => [
					new Image([
						'maxSize' => '1024k',
						'mimeTypesMessage' => 'Merci d\'utiliser un format d\'image valide.',
					])
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
