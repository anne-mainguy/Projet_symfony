<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * fonction applicable a tous les formulaires qui hériteront de cette class
     *
     * @param string $label
     * @param string $required
     * @param string $placeholder
     * @param string $minlength
     * @param string $maxlength
     * @return array
     */
    public function getConfiguration($label, $required, $placeholder = null, $minlength = null, $maxlength = null ){
        return [
            'label' => $label,
            'required' => $required,
            'attr' => [
                'placeholder' => $placeholder,
                'minlength' => $minlength,
                'maxlength' => $maxlength
            ]
        ];
    }
}
