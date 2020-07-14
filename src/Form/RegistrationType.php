<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, $this->getConfiguration("Votre pseudo", true))
            ->add('pass', PasswordType::class, $this->getConfiguration("Mot de passe", true, 'Entre 8 et 12 caractères', 8, 12))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Comfirmation du mot de passe", true))
            ->add('email', EmailType::class, $this->getConfiguration("Email" , true))
            ->add('avatarFile', FileType::class, [
                'label' => 'Selectionné votre avatar',
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
                        'mimeTypesMessage' => 'Fichier jpg, jpeg et png uniquement',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
