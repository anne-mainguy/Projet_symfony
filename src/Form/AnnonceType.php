<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Themes;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre a donner a l'image", false, "Donnez un nom a votre paréidolie...", null, 100) )
            ->add('description', TextareaType::class, $this->getConfiguration("Description", false, "Vous pouvez y ajouter une description."))
            ->add('theme', EntityType::class, [
                'class' => Themes::class,
                'choice_label' => 'theme'
            ])
            ->add('image', FileType::class, [
                'label' => 'Le fichier image',
                'required' => true,
                'attr' => [
                    'accept' => '.png, .jpg, .jpeg',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'maxSizeMessage' => 'Le Fichier selectionné est trop lourd.',
                        'mimeTypesMessage' => 'Fichier jpg, jpeg et png uniquement',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
