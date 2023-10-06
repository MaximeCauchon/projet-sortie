<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RechercheSortiesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'required' => true, 
                'label' => 'Rechercher par campus', 
                'choice_label' => 'nom'
            ])
            ->add('nom', TextType::class, [
                'required' => false, 
                'label' => 'Rechercher par nom', 
            ])
            ->add('dateDebut', DateType::class, [
                'required' => false, 
                'widget' => 'single_text', 
                'label' => 'Date de début', 
            ])
            ->add('dateFin', DateType::class, [
                'required' => false, 
                'widget' => 'single_text', 
                'label' => 'Date de fin', 
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false, 
                'label' => "Sorties dont je suis l'organisateur", 
            ])
            ->add('inscrit', CheckboxType::class, [
                'required' => false, 
                'label' => "Sorties auxquelles je suis inscrit", 
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties auxquelles je ne suis pas inscrit/e", 
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'required' => false, 
                'label' => "Sorties passées",
            ])
            ->add('rechercher', SubmitType::class, ['label' => 'Rechercher']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}
