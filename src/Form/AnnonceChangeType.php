<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Themes;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceChangeType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theme', EntityType::class, [
                'class' => Themes::class,
                'choice_label' => 'theme'
            ])
            ->add('title', TextType::class, $this->getConfiguration("Titre a donner a l'image", false, "Donnez un nom a votre paréidolie...", null, 100) )
            ->add('description', TextareaType::class, $this->getConfiguration("Description", false, "Vous pouvez y ajouter une description."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
