<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NouveauLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
				'label' => 'Nom du lieu :'
			])
            ->add('rue', TextType::class, [
				'label' => 'Rue :'
			])
            ->add('latitude', TextType::class, [
				'label' => 'Latitude :',
                'required' => false
			])
            ->add('longitude', TextType::class, [
				'label' => 'Longitude :',
                'required' => false
			])
            ->add('ville', EntityType::class, [
				'label' => 'Ville :',
				'class' => Ville::class,
				'choice_label' => 'nom',
				'placeholder' => '-- Sélectionner une ville --',
				'placeholder_attr' =>
					['disabled' => 'disabled'],
			])
            ->add('ajout_lieu', SubmitType::class, ['label' => 'Ajouter le lieu'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
