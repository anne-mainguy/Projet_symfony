<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AccountType extends AbstractType
{
    //form pour modification de profile
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('controlleAvatar', TextType::class, [
                'mapped' => false,
                'required' => false
                
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Selectionnez un fichier',
                'attr' => [
                    'accept' => '.png, .jpg, .jpeg',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'maxSizeMessage' => 'Le Fichier selectionné est trop lourd.',
                        'mimeTypesMessage' => 'Fichier jpg, jpeg et png uniquement.',
                    ])
                ],
            ])
            ->add('pseudo')
            ->add('email')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
