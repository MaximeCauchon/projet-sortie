<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class UploadParticipantViaCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('campus', EntityType::class, [
				'class' => Campus::class,
				'choice_label' => 'nom',
				'mapped' => false,
			])
			->add('filePart', FileType::class, [
				'label' => 'Fichier CSV',
				'mapped' => false,
				'required' => true,
				'constraints' => [
					new File([
						'maxSize' => '1024k',
//						'mimeTypes' => [
//							'text/csv',
//							'text/csv-schema',
//							'application/csvm+json'
//						],
						'mimeTypesMessage' => 'Le document doit être un fichier CSV valide.',
					])
				],
			])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
