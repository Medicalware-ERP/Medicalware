<?php

namespace App\Form\Base;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditorType extends AbstractType
{
    public const CONFIG = [
        'toolbar' => [
            [
                'Bold', 'Italic', 'Underline', 'TextColor', 'FontSize', 'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'
            ]
        ],
    ];

    public function getParent(): string
    {
        return CKEditorType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'config'        => self::CONFIG,
            'input_sync'    => true
        ]);
    }
}